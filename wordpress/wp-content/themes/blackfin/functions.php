<?php
/**
 * Frontend assets, site navigation and the shared quote form.
 */

require_once __DIR__ . '/icons.php';

function blackfin_service_areas() {
	return array( 'Tampa', 'St. Pete', 'Dunedin', 'Largo', 'Palm Harbor', 'Tarpon Springs', 'Clearwater', 'Belleaire', 'Wesley Chapel', 'Lutz', 'Sarasota', 'Bradenton' );
}

add_action( 'after_setup_theme', function () {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/site.css' );
} );

add_action( 'wp_enqueue_scripts', function () {
	$theme = get_stylesheet_directory();
	wp_enqueue_style( 'blackfin', get_theme_file_uri( 'assets/site.css' ), array(), filemtime( $theme . '/assets/site.css' ) );
	wp_enqueue_script( 'blackfin', get_theme_file_uri( 'assets/site.js' ), array(), filemtime( $theme . '/assets/site.js' ), true );
} );

add_filter( 'render_block_data', function ( $block ) {
	if ( 'core/navigation' === $block['blockName'] && empty( $block['attrs']['ref'] ) ) {
		$classes = explode( ' ', $block['attrs']['className'] ?? '' );
		$menu = (int) get_option( 'blackfin_navigation_id' );
		if ( in_array( 'bf-menu', $classes, true ) && 'wp_navigation' === get_post_type( $menu ) ) {
			$block['attrs']['ref'] = $menu;
		}
	}
	return $block;
} );

add_action( 'template_redirect', function () {
	global $wp;
	if ( 'home-page/contact' === trim( $wp->request, '/' ) ) {
		$contact = get_page_by_path( 'contact' );
		if ( $contact && 'publish' === $contact->post_status ) {
			wp_safe_redirect( get_permalink( $contact ), 301 );
			exit;
		}
	}
}, 1 );

add_action( 'wp_head', function () {
	if ( ! is_page() ) {
		return;
	}
	$description = get_post_meta( get_queried_object_id(), '_blackfin_description', true );
	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}
	if ( is_front_page() ) {
		$business = array(
			'@context' => 'https://schema.org',
			'@type' => 'GeneralContractor',
			'@id' => home_url( '/#blackfin' ),
			'name' => get_bloginfo( 'name' ),
			'url' => home_url( '/' ),
			'telephone' => '+1-727-330-6041',
			'areaServed' => blackfin_service_areas(),
			'sameAs' => array( 'https://www.instagram.com/blackfincon/' ),
		);
		printf( '<script type="application/ld+json">%s</script>' . "\n", wp_json_encode( $business, JSON_HEX_TAG | JSON_HEX_AMP ) );
	}
} );

add_shortcode( 'blackfin_reviews', function () {
	$pattern = get_post( (int) get_option( 'blackfin_reviews_pattern_id' ) );
	if ( ! $pattern || 'wp_block' !== $pattern->post_type || 'publish' !== $pattern->post_status || ! trim( wp_strip_all_tags( $pattern->post_content ) ) ) {
		return '';
	}
	return '<section class="bf-detail-section bf-reviews"><div class="bf-detail-wrap"><h2 class="bf-detail-heading">Google reviews</h2>'
		. do_blocks( $pattern->post_content ) . '</div></section>';
} );

add_shortcode( 'blackfin_instagram', function ( $attrs ) {
	$attrs = shortcode_atts( array( 'feed' => 0 ), $attrs );
	$id = absint( $attrs['feed'] );
	$feed = $id && shortcode_exists( 'instagram-feed' ) ? do_shortcode( sprintf( '[instagram-feed feed="%d"]', $id ) ) : '';
	return '<section class="bf-detail-band bf-instagram"><div class="bf-detail-wrap"><h2 class="bf-detail-heading">Blackfin on Instagram</h2>'
		. $feed . '<p class="bf-detail-link"><a href="https://www.instagram.com/blackfincon/">See project updates on Instagram</a></p></div></section>';
} );

add_shortcode( 'blackfin_quote_form', function () {
	$form_id = (int) get_option( 'blackfin_quote_form_id' );
	if ( ! $form_id || 'wpcf7_contact_form' !== get_post_type( $form_id ) || ! shortcode_exists( 'contact-form-7' ) ) {
		return '<p>The quote form is currently unavailable. Please call <a href="tel:+17273306041">(727) 330-6041</a>.</p>';
	}
	return do_shortcode( sprintf( '[contact-form-7 id="%d"]', $form_id ) );
} );

add_filter( 'wpcf7_autop_or_not', function ( $enabled, $options ) {
	$form = WPCF7_ContactForm::get_current();
	if ( $form && (int) $form->id() === (int) get_option( 'blackfin_quote_form_id' ) && 'mail' !== ( $options['for'] ?? '' ) ) {
		return false;
	}
	return $enabled;
}, 10, 2 );

