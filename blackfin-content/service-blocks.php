<?php
/** Native editor blocks for the service pages, service menus and About page. */

function bf_detail_hero( $title, $intro, $image, $alt, $cta, $target ) {
	$copy = bf_text( 'Tampa Bay, Florida', 'bf-detail-location' )
		. bf_heading( $title, 'bf-detail-title', 1 )
		. bf_text( esc_html( $intro ), 'bf-detail-intro' )
		. bf_buttons( 'bf-detail-actions', bf_button( $cta, $target, 'primary', 'md' ) );
	return bf_group( 'bf-detail-hero', bf_group( 'bf-detail-wrap bf-detail-split',
		bf_group( 'bf-detail-copy', $copy ) . bf_image( $image, $alt, 'bf-detail-photo', 'large' ) ), 'section' );
}

function bf_detail_link( $slug, $title ) {
	return bf_text( bf_link( bf_url( '/' . $slug ), $title ), 'bf-detail-link' );
}

function bf_service_directory( $services, $media ) {
	$cards = '';
	foreach ( $services as $slug => $service ) {
		$card = bf_image( $media[ $service['image'] ], $service['alt'], 'bf-directory-photo', 'medium_large' )
			. bf_heading( $service['title'], 'bf-directory-title', 3 )
			. bf_text( esc_html( $service['summary'] ), 'bf-directory-summary' )
			. bf_detail_link( $slug, 'Explore ' . strtolower( $service['title'] ) );
		$cards .= bf_group( 'bf-directory-card', $card );
	}
	return bf_group( 'bf-directory', bf_group( 'bf-detail-wrap',
		bf_heading( 'Our Services', 'bf-detail-heading' ) . bf_group( 'bf-directory-grid', $cards ) ), 'section', 'blackfin-services' );
}

function bf_service_page( $slug, $services, $media ) {
	$service = $services[ $slug ];
	$content = bf_detail_hero( $service['title'], $service['intro'], $media[ $service['image'] ],
		$service['alt'], 'Request a free quote', '/contact' );
	$details = '';
	foreach ( $service['sections'] as list( $heading, $body ) ) {
		$details .= bf_group( 'bf-scope-item', bf_heading( $heading, 'bf-scope-title', 3 )
			. bf_text( esc_html( $body ), 'bf-detail-body' ) );
	}
	$content .= bf_group( 'bf-detail-section', bf_group( 'bf-detail-wrap',
		bf_heading( $service['heading'], 'bf-detail-heading' ) . bf_group( 'bf-scope-grid', $details ) ), 'section' );
	$next = $service['related'];
	$answer = bf_heading( $service['question'], 'bf-detail-heading' )
		. bf_text( esc_html( $service['answer'] ), 'bf-detail-body' )
		. bf_buttons( 'bf-detail-actions', bf_button( 'Discuss your project', '/contact' ) );
	$links = bf_heading( 'Related pages', 'bf-scope-title', 3 )
		. bf_detail_link( $next, $services[ $next ]['title'] )
		. bf_detail_link( 'about-us/#service-areas', 'Our Tampa Bay service areas' );
	return $content . bf_group( 'bf-detail-band', bf_group( 'bf-detail-wrap bf-detail-two',
		bf_group( 'bf-detail-answer', $answer ) . bf_group( 'bf-detail-aside', $links ) ), 'section' );
}

function bf_service_links( $slugs, $services, $heading ) {
	$links = '';
	foreach ( $slugs as $slug ) {
		$links .= bf_detail_link( $slug, $services[ $slug ]['title'] );
	}
	return bf_group( 'bf-service-links', bf_group( 'bf-detail-wrap',
		bf_heading( $heading, 'bf-detail-heading' ) . bf_group( 'bf-service-linkrow', $links ) ), 'section' );
}

function bf_about_page( $services, $media ) {
	$content = bf_detail_hero( 'About Blackfin Construction & Painting',
		'Blackfin works with homeowners, property managers and community associations across Tampa Bay. Our work ranges from kitchens and bathrooms to exterior painting and coatings for occupied commercial buildings.',
		$media['commercial-hero-3.jpg'], 'Two people standing outside a building beside a raised boom lift', 'Explore our service areas', '#service-areas' );
	$homes = bf_heading( 'Work on your home', 'bf-scope-title', 3 )
		. bf_text( 'Some projects start with a worn finish. Others start with a room that no longer works for the people using it. Our residential services cover painting, remodeling, buildouts and outdoor spaces.', 'bf-detail-body' )
		. bf_detail_link( 'residential', 'Residential services' );
	$properties = bf_heading( 'Work on a shared property', 'bf-scope-title', 3 )
		. bf_text( 'For condominiums, associations and commercial buildings, the people using the property are part of the planning. We work with property managers and boards on access, preparation, painting and specialty coatings.', 'bf-detail-body' )
		. bf_detail_link( 'commercial', 'Commercial services' );
	$content .= bf_group( 'bf-detail-section', bf_group( 'bf-detail-wrap', bf_heading( 'Who we work with', 'bf-detail-heading' )
		. bf_group( 'bf-about-columns', bf_group( 'bf-scope-item', $homes ) . bf_group( 'bf-scope-item', $properties ) ) ), 'section' );
	$locations = bf_heading( 'Our Tampa Bay service areas', 'bf-detail-heading' )
		. bf_text( 'Blackfin serves the following communities. If your project is nearby, send us the address and a short description so we can confirm coverage.', 'bf-detail-body' )
		. bf_list( blackfin_service_areas(), 'bf-area-list' )
		. bf_buttons( 'bf-detail-actions', bf_button( 'Ask about your project', '/contact' ) );
	$content .= bf_group( 'bf-detail-band', bf_group( 'bf-detail-wrap', $locations ), 'section', 'service-areas' );
	return $content . bf_service_links( array_keys( $services ), $services, 'Explore our services' )
		. bf_block( 'shortcode', '[blackfin_reviews]' );
}
