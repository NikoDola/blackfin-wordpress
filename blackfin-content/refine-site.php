<?php
/** Apply the UI revision to seeded blocks in the existing local WordPress site. */

function bf_save_refinement( $id, $content ) {
	if ( $content !== get_post_field( 'post_content', $id, 'raw' ) ) {
		$result = wp_update_post( wp_slash( array( 'ID' => $id, 'post_content' => $content ) ), true );
		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
		}
	}
	update_post_meta( $id, '_blackfin_ui_refined', 1 );
}

function bf_refine_quote_links( $html, $title = '' ) {
	$tags = new WP_HTML_Tag_Processor( $html );
	while ( $tags->next_tag( array( 'tag_name' => 'A', 'class_name' => 'wp-block-button__link' ) ) ) {
		if ( bf_url( '/contact' ) === $tags->get_attribute( 'href' ) ) {
			$tags->set_attribute( 'href', bf_url( bf_quote_path( $title ) ) );
		}
	}
	return $tags->get_updated_html();
}

function bf_refine_overview( $id, $route, $page, $services, $media ) {
	if ( get_post_meta( $id, '_blackfin_ui_refined', true ) ) {
		return;
	}
	$home = 'home' === $route;
	$content = '';
	$blocks = parse_blocks( get_post_field( 'post_content', $id, 'raw' ) );
	$featured = array();
	$classes = array_map( static fn( $block ) => $block['attrs']['className'] ?? '', $blocks );
	$directory_class = in_array( 'bf-directory', $classes, true ) ? 'bf-directory' : 'bf-services-section';
	foreach ( $blocks as $block ) {
		$class = $block['attrs']['className'] ?? '';
		$html = serialize_block( $block );
		if ( 'bf-hero-section' === $class && str_contains( $html, 'bf-hero-scrollIndicator' ) ) {
			$html = bf_hero( $page['slides'], $media, $home ? '/#blackfin-services' : '#services' );
		}
		if ( $home && $directory_class === $class ) {
			$html = bf_service_directory( $services, $media ) . bf_gallery( $page, $media, true ) . bf_home_overview_links();
		} elseif ( $home && str_starts_with( $class, 'bf-feature-section ' ) ) {
			$featured[] = $html;
			$html = '';
		} elseif ( 'bf-gallery-section' === $class ) {
			$html = $home ? '' : bf_gallery( $page, $media );
		} elseif ( $home && in_array( $class, array( 'bf-gallery-section bf-gallery-featured', 'bf-service-links bf-home-overviews' ), true ) ) {
			// The revised directory already includes these two sections.
			$html = '';
		} elseif ( 'bf-commercial-servicesSection' === $class && empty( $block['attrs']['anchor'] ) ) {
			$block['attrs']['anchor'] = 'services';
			$block['innerContent'][0] = str_replace( '<section class=', '<section id="services" class=', $block['innerContent'][0] );
			$html = serialize_block( $block );
		}
		$content .= bf_refine_quote_links( $html );
	}
	// Preserve any distinct feature blocks that were added in the editor.
	foreach ( $featured as $feature ) {
		$stock_feature = false;
		foreach ( $page['features'] as $stock ) {
			$stock = str_replace( '#request-a-quote', '', bf_feature( $stock, $media ) );
			if ( trim( $stock ) === trim( $feature ) ) {
				$stock_feature = true;
				break;
			}
		}
		if ( ! $stock_feature ) {
			$content .= bf_refine_quote_links( $feature );
		}
	}
	bf_save_refinement( $id, $content );
}

function bf_refine_service( $id, $service, $media ) {
	if ( get_post_meta( $id, '_blackfin_ui_refined', true ) ) {
		return;
	}
	$content = get_post_field( 'post_content', $id, 'raw' );
	$content = bf_refine_quote_links( $content, $service['title'] );
	if ( isset( $service['prep'] ) && ! str_contains( $content, 'bf-service-preparation' ) ) {
		$previous_id = $media[ $service['prep']['image'] ];
		$blocks = parse_blocks( $content );
		$content = '';
		foreach ( $blocks as $block ) {
			$class = $block['attrs']['className'] ?? '';
			$html = serialize_block( $block );
			if ( 'bf-detail-hero' === $class ) {
				// Replace the seeded hero image and keep editor-authored text.
				$pattern = '~<!-- wp:image \{(?=[^}]*"id":' . $previous_id . '(?:,|\}))[^}]*\} -->\s*<figure[^>]*>.*?</figure>\s*<!-- /wp:image -->~s';
				$html = preg_replace( $pattern, trim( bf_image( $media[ $service['image'] ], $service['alt'], 'bf-detail-photo', 'large' ) ), $html, 1 );
			}
			if ( 'bf-detail-band' === $class ) {
				$html = bf_service_preparation( $service, $media ) . $html;
			}
			$content .= $html;
		}
	}
	bf_save_refinement( $id, $content );
}

function bf_refine_site( $ids, $quote, $pages, $services, $media ) {
	foreach ( array( 'home', 'residential', 'commercial' ) as $route ) {
		bf_refine_overview( $ids[ $route ], $route, $pages[ $route ], $services, $media );
	}
	foreach ( $services as $slug => $service ) {
		bf_refine_service( $ids[ $slug ], $service, $media );
	}
	if ( ! get_post_meta( $ids['about-us'], '_blackfin_ui_refined', true ) ) {
		$about = get_post_field( 'post_content', $ids['about-us'], 'raw' );
		bf_save_refinement( $ids['about-us'], bf_refine_quote_links( $about ) );
	}

	if ( ! get_post_meta( $ids['contact'], '_blackfin_ui_refined', true ) ) {
		$content = '';
		foreach ( parse_blocks( get_post_field( 'post_content', $ids['contact'], 'raw' ) ) as $block ) {
			$content .= 'bf-contact-pageHero' === ( $block['attrs']['className'] ?? '' ) ? bf_contact_hero() : serialize_block( $block );
		}
		bf_save_refinement( $ids['contact'], $content );
	}
	if ( ! get_post_meta( $quote, '_blackfin_ui_refined', true ) ) {
		bf_save_refinement( $quote, bf_quote_section() );
	}

	$form_id = (int) get_option( 'blackfin_quote_form_id' );
	if ( get_post_meta( $form_id, '_blackfin_ui_refined', true ) ) {
		return;
	}
	$form = WPCF7_ContactForm::get_instance( $form_id );
	$properties = $form->get_properties();
	if ( ! str_contains( $properties['form'], 'default:get' ) ) {
		$properties['form'] = file_get_contents( __DIR__ . '/quote-form.html' );
		$form->set_properties( $properties );
		if ( ! $form->save() ) {
			WP_CLI::error( 'Could not update the local quote form.' );
		}
	}
	update_post_meta( $form_id, '_blackfin_ui_refined', 1 );
}
