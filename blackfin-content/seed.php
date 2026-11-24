<?php
/**
 * Run with WP-CLI against the workspace's WordPress Studio site.
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit;
}
$workspace = dirname( __DIR__ );
$source_site = realpath( $workspace . '/wordpress' );
if ( ! $source_site || $source_site !== realpath( ABSPATH ) || ! in_array( wp_parse_url( home_url(), PHP_URL_HOST ), array( 'localhost', '127.0.0.1', '::1' ), true ) ) {
	WP_CLI::error( 'The seed targets the localhost WordPress install in this workspace.' );
}
if ( 'blackfin' !== get_stylesheet() || ! class_exists( 'WPCF7_ContactForm' ) ) {
	WP_CLI::error( 'Activate the Blackfin theme and Contact Form 7 before running the seed.' );
}

require_once __DIR__ . '/blocks.php';
require_once __DIR__ . '/service-blocks.php';
require_once __DIR__ . '/expand-site.php';
require_once __DIR__ . '/refine-site.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

function bf_insert_once( $type, $slug, $title, $content = '', $meta = array() ) {
	$existing = get_page_by_path( $slug, OBJECT, $type );
	if ( $existing ) {
		return (int) $existing->ID;
	}
	$post_id = wp_insert_post( wp_slash( array(
		'post_type' => $type,
		'post_name' => $slug,
		'post_title' => $title,
		'post_content' => $content,
		'post_status' => 'publish',
		'comment_status' => 'closed',
		'ping_status' => 'closed',
		'meta_input' => $meta,
	) ), true );
	if ( is_wp_error( $post_id ) ) {
		WP_CLI::error( $post_id->get_error_message() );
	}
	return (int) $post_id;
}

function bf_import_photos( $dir ) {
	$files = glob( $dir . '/*.{jpg,webp}', GLOB_BRACE );
	$media = array();
	if ( ! $files ) {
		WP_CLI::error( 'Reference images were not found.' );
	}
	foreach ( $files as $file ) {
		$filename = basename( $file );
		$matches = get_posts( array(
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'fields' => 'ids',
			'posts_per_page' => 1,
			'meta_key' => '_blackfin_source_filename',
			'meta_value' => $filename,
		) );
		if ( $matches ) {
			$media[ $filename ] = (int) $matches[0];
			continue;
		}
		$copy = wp_tempnam( $filename );
		if ( ! $copy || ! copy( $file, $copy ) ) {
			WP_CLI::error( "Could not stage $filename for import." );
		}
		$title = 'logo.jpg' === $filename ? 'Blackfin Construction & Painting' : ucwords( str_replace( '-', ' ', pathinfo( $filename, PATHINFO_FILENAME ) ) );
		$attachment = media_handle_sideload( array( 'name' => $filename, 'tmp_name' => $copy ), 0, $title );
		if ( is_wp_error( $attachment ) ) {
			wp_delete_file( $copy );
			WP_CLI::error( $attachment->get_error_message() );
		}
		add_post_meta( $attachment, '_blackfin_source_filename', $filename, true );
		if ( 'logo.jpg' === $filename ) {
			update_post_meta( $attachment, '_wp_attachment_image_alt', $title );
		}
		$media[ $filename ] = (int) $attachment;
	}
	return $media;
}

function bf_quote_form_id() {
	$existing = (int) get_option( 'blackfin_quote_form_id' );
	if ( $existing && WPCF7_ContactForm::get_instance( $existing ) ) {
		return $existing;
	}
	$previous = get_page_by_path( 'blackfin-quote-request', OBJECT, 'wpcf7_contact_form' );
	if ( $previous ) {
		return (int) $previous->ID;
	}
	$form = WPCF7_ContactForm::get_template( array( 'title' => 'Blackfin Quote Request' ) );
	$properties = $form->get_properties();
	$properties['form'] = file_get_contents( __DIR__ . '/quote-form.html' );
	$properties['mail'] = array_replace( $properties['mail'], array(
		'recipient' => 'blackfin-quotes@example.test',
		'sender' => 'Blackfin Local <wordpress@example.test>',
		'subject' => 'Blackfin quote request',
		'additional_headers' => 'Reply-To: [your-email]',
		'body' => "Name: [your-name]\nPhone: [your-tel]\nEmail: [your-email]\nSubject: [your-subject]\n\n[your-message]",
	) );
	$form->set_properties( $properties );
	$id = (int) $form->save();
	if ( ! $id ) {
		WP_CLI::error( 'Could not create the local quote form.' );
	}
	return $id;
}

function bf_hide_starter_content() {
	$defaults = array(
		array( 'sample-page', 'page', 'Sample Page', 'This is an example page.' ),
		array( 'hello-world', 'post', 'Hello world!', 'Welcome to WordPress. This is your first post.' ),
	);
	foreach ( $defaults as list( $slug, $type, $title, $text ) ) {
		$post = get_page_by_path( $slug, OBJECT, $type );
		if ( $post && 'publish' === $post->post_status && $title === $post->post_title && str_contains( $post->post_content, $text ) ) {
			$result = wp_update_post( array( 'ID' => $post->ID, 'post_status' => 'draft' ), true );
			if ( is_wp_error( $result ) ) {
				WP_CLI::warning( $result->get_error_message() );
			}
		}
	}
}

$pages = json_decode( file_get_contents( __DIR__ . '/pages.json' ), true, 512, JSON_THROW_ON_ERROR );
$services = json_decode( file_get_contents( __DIR__ . '/services.json' ), true, 512, JSON_THROW_ON_ERROR );
$media = bf_import_photos( $workspace . '/blackfin-reference/public/images' );
update_option( 'blackfin_quote_form_id', bf_quote_form_id() );
$quote = bf_insert_once( 'wp_block', 'blackfin-quote-section', 'Blackfin Quote Section', bf_quote_section() );
$routes = array( 'home' => 'Home', 'residential' => 'Residential', 'commercial' => 'Commercial', 'contact' => 'Contact' );
$ids = array();

foreach ( $routes as $route => $title ) {
	$page = $pages[ $route ];
	if ( 'contact' === $route ) {
		$content = bf_contact_hero();
	} elseif ( 'home' === $route ) {
		$content = bf_hero( $page['slides'], $media, '/#blackfin-services' ) . bf_service_directory( $services, $media )
			. bf_gallery( $page, $media, true ) . bf_home_overview_links()
			. bf_block( 'shortcode', '[blackfin_reviews]' ) . bf_block( 'shortcode', '[blackfin_instagram]' );
	} else {
		$content = bf_hero( $page['slides'], $media, '#services' ) . bf_service_lists( $route, $page['services'] );
		foreach ( $page['features'] as $feature ) {
			$content .= bf_feature( $feature, $media );
		}
		$content .= bf_gallery( $page, $media );
	}
	$content .= bf_block( 'block', null, array( 'ref' => $quote ) );
	$ids[ $route ] = bf_insert_once( 'page', $route, $page['title'], $content,
		array( '_blackfin_description' => $page['description'] ) );
}

$privacy = get_page_by_path( 'privacy-policy' );
if ( $privacy && 'draft' === $privacy->post_status && ! metadata_exists( 'post', $privacy->ID, '_blackfin_seeded' ) && ! get_option( 'blackfin_seeded' ) ) {
	$content = bf_group( 'bf-policy', file_get_contents( __DIR__ . '/privacy.html' ) );
	wp_update_post( wp_slash( array( 'ID' => $privacy->ID, 'post_status' => 'publish', 'post_content' => $content,
		'meta_input' => array( '_blackfin_seeded' => '1' ) ) ), true );
	$policy = (int) $privacy->ID;
} else {
	$policy = bf_insert_once( 'page', 'privacy-policy', 'Privacy Policy',
		bf_group( 'bf-policy', file_get_contents( __DIR__ . '/privacy.html' ) ) );
}

$first_run = ! get_option( 'blackfin_seeded' );
if ( $first_run ) {
	global $wp_rewrite;
	update_option( 'blogname', 'Blackfin Construction & Painting' );
	update_option( 'blogdescription', 'Tampa Bay' );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['home'] );
	update_option( 'wp_page_for_privacy_policy', $policy );
	update_option( 'site_logo', $media['logo.jpg'] );
	update_option( 'site_icon', $media['logo.jpg'] );
	update_option( 'blog_public', '0' );
	bf_hide_starter_content();
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	flush_rewrite_rules( false );
}
$navigation = '';
foreach ( $routes as $route => $title ) {
	$navigation .= bf_block( 'navigation-link', null, array(
		'label' => $title,
		'type' => 'page',
		'id' => $ids[ $route ],
		'url' => bf_url( 'home' === $route ? '/' : '/' . $route ),
		'kind' => 'post-type',
		'isTopLevelLink' => true,
	) );
}
$menu = bf_insert_once( 'wp_navigation', 'blackfin-main', 'Blackfin Main Menu', $navigation );
update_option( 'blackfin_navigation_id', $menu );
foreach ( $services as $slug => $service ) {
	$ids[ $slug ] = bf_insert_once( 'page', $slug, $service['title'], bf_service_page( $slug, $services, $media ),
		array( '_blackfin_description' => $service['description'] ) );
}
$ids['about-us'] = bf_insert_once( 'page', 'about-us', 'About Us', bf_about_page( $services, $media ),
	array( '_blackfin_description' => 'Meet Blackfin Construction & Painting and explore the Tampa Bay communities we serve for residential and commercial projects.' ) );
add_post_meta( $policy, '_blackfin_description', 'Read how Blackfin Construction & Painting handles information collected through its website.', true );
$review_template = bf_block( 'quote', '<blockquote class="wp-block-quote">' . bf_text( '' ) . '</blockquote>' );
update_option( 'blackfin_reviews_pattern_id', bf_insert_once( 'wp_block', 'blackfin-google-reviews', 'Blackfin Selected Google Reviews', $review_template ) );
foreach ( array( 'residential', 'commercial' ) as $route ) {
	bf_expand_overview( $ids[ $route ], $route, $services );
}
bf_expand_menu( $menu, $ids, $services );
$commercial_content = get_post_field( 'post_content', $ids['commercial'], 'raw' );
$old_claim = '<li>' . esc_html( "Tampa Bay's #1 Commercial Contractor" ) . '</li>';
if ( str_contains( $commercial_content, $old_claim ) ) {
	bf_apply_expansion( $ids['commercial'], str_replace( $old_claim, '<li>Tampa Bay service area</li>', $commercial_content ) );
}
bf_refine_site( $ids, $quote, $pages, $services, $media );
update_option( 'blackfin_seeded', '1' );
WP_CLI::success( sprintf( 'Blackfin content available: %d images, %d pages, 1 shared quote section.', count( $media ), count( $ids ) + 1 ) );

