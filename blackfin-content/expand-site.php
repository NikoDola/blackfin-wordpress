<?php
/** Upgrade the original seed in place. Existing editor content stays editable after this import. */

function bf_apply_expansion( $id, $content ) {
	if ( get_post_field( 'post_content', $id, 'raw' ) !== $content ) {
		$result = wp_update_post( wp_slash( array( 'ID' => $id, 'post_content' => $content ) ), true );
		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
		}
	}
	update_post_meta( $id, '_blackfin_service_expansion', 1 );
}

function bf_expand_overview( $id, $route, $services ) {
	if ( get_post_meta( $id, '_blackfin_service_expansion', true ) ) {
		return;
	}
	$blocks = parse_blocks( get_post_field( 'post_content', $id, 'raw' ) );
	$expanded = '';
	$links_added = false;
	$slugs = 'commercial' === $route ? array( 'exterior-painting', 'interior-painting' ) : array_keys( $services );
	$directory = bf_service_links( $slugs, $services, 'Explore ' . $route . ' services' );
	foreach ( $blocks as $block ) {
		$html = serialize_block( $block );
		$class = $block['attrs']['className'] ?? '';
		if ( in_array( $class, array( 'bf-res-servicesSection', 'bf-commercial-servicesSection' ), true ) ) {
			$html .= $directory;
			$links_added = true;
		}
		$expanded .= $html;
	}
	bf_apply_expansion( $id, $expanded . ( $links_added ? '' : $directory ) );
}

function bf_expand_menu( $menu, $ids, $services ) {
	if ( get_post_meta( $menu, '_blackfin_service_expansion', true ) ) {
		return;
	}
	$link = static function ( $slug, $label ) use ( $ids ) {
		return bf_block( 'navigation-link', null, array(
			'label' => $label, 'type' => 'page', 'id' => $ids[ $slug ],
			'url' => bf_url( '/' . $slug ), 'kind' => 'post-type',
		) );
	};
	$children = '';
	foreach ( $services as $slug => $service ) {
		$children .= $link( $slug, $service['title'] );
	}
	$submenu = bf_block( 'navigation-submenu', $children, array( 'label' => 'Services', 'type' => 'custom',
		'kind' => 'custom', 'url' => bf_url( '/#blackfin-services' ) ) );
	$about = $link( 'about-us', 'About Us' );
	$content = '';
	foreach ( parse_blocks( get_post_field( 'post_content', $menu, 'raw' ) ) as $block ) {
		$page_id = $block['attrs']['id'] ?? 0;
		if ( $ids['contact'] === $page_id ) {
			$content .= $about;
			$about = '';
		}
		$content .= serialize_block( $block );
		if ( $ids['home'] === $page_id ) {
			$content .= $submenu;
			$submenu = '';
		}
	}
	bf_apply_expansion( $menu, $content . $submenu . $about );
}
