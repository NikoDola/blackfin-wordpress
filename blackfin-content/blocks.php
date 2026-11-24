<?php
/** Build the editable block markup used by the seed. */

function bf_block( $name, $html = null, $attributes = array() ) {
	$json = $attributes ? ' ' . serialize_block_attributes( $attributes ) : '';
	if ( null === $html ) {
		return "<!-- wp:$name$json /-->\n";
	}
	return "<!-- wp:$name$json -->\n$html\n<!-- /wp:$name -->\n";
}

function bf_html( $html ) {
	return bf_block( 'html', $html );
}

function bf_group( $classes, $children, $tag = 'div', $anchor = '' ) {
	$attrs = array( 'className' => $classes, 'layout' => array( 'type' => 'default' ) );
	if ( 'div' !== $tag ) {
		$attrs['tagName'] = $tag;
	}
	if ( $anchor ) {
		$attrs['anchor'] = $anchor;
	}
	$id = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
	return bf_block( 'group', "<$tag$id class=\"wp-block-group $classes\">\n$children</$tag>", $attrs );
}

function bf_text( $html, $classes = '' ) {
	$attr = $classes ? ' class="' . esc_attr( $classes ) . '"' : '';
	return bf_block( 'paragraph', "<p$attr>$html</p>", $classes ? array( 'className' => $classes ) : array() );
}

function bf_heading( $text, $classes, $level = 2 ) {
	$text = esc_html( $text );
	return bf_block( 'heading', "<h$level class=\"wp-block-heading $classes\">$text</h$level>", array( 'level' => $level, 'className' => $classes ) );
}

function bf_link( $url, $label, $classes = '' ) {
	return '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $classes ) . '">' . esc_html( $label ) . '</a>';
}

function bf_url( $path ) {
	if ( str_starts_with( $path, '#' ) ) {
		return $path;
	}
	$parts = explode( '#', $path, 2 );
	$path_and_query = explode( '?', $parts[0], 2 );
	$url = home_url( trailingslashit( $path_and_query[0] ) );
	if ( isset( $path_and_query[1] ) ) {
		$url .= '?' . $path_and_query[1];
	}
	return $url . ( isset( $parts[1] ) ? '#' . $parts[1] : '' );
}

function bf_quote_path( $project = '' ) {
	$query = $project ? '?your-subject=' . rawurlencode( $project ) : '';
	return '/contact/' . $query . '#request-a-quote';
}

function bf_button( $label, $path, $variant = 'primary', $size = 'md' ) {
	$classes = "bf-button-btn bf-button-$variant bf-button-$size";
	$link = bf_link( bf_url( $path ), $label, 'wp-block-button__link wp-element-button' );
	return bf_block( 'button', "<div class=\"wp-block-button $classes\">$link</div>", array( 'className' => $classes ) );
}

function bf_buttons( $classes, $buttons ) {
	return bf_block( 'buttons', "<div class=\"wp-block-buttons $classes\">\n$buttons</div>", array( 'className' => $classes ) );
}

function bf_image( $attachment, $alt, $classes = '', $size = 'full', $caption = '' ) {
	$url = esc_url( wp_get_attachment_image_url( $attachment, $size ) );
	$alt = esc_attr( $alt );
	$classes = trim( $classes );
	$custom = $classes ? " $classes" : '';
	$caption = $caption ? '<figcaption class="wp-element-caption">' . esc_html( $caption ) . '</figcaption>' : '';
	return bf_block(
		'image',
		"<figure class=\"wp-block-image size-$size$custom\"><img src=\"$url\" alt=\"$alt\" class=\"wp-image-$attachment\"/>$caption</figure>",
		array( 'id' => $attachment, 'sizeSlug' => $size, 'linkDestination' => 'none', 'className' => $classes )
	);
}

function bf_image_id( $path, $media ) {
	return $media[ basename( $path ) ];
}

function bf_list( $items, $classes ) {
	$markup = '';
	foreach ( $items as $item ) {
		$markup .= bf_block( 'list-item', '<li>' . esc_html( $item ) . '</li>' );
	}
	return bf_block( 'list', "<ul class=\"wp-block-list $classes\">\n$markup</ul>", array( 'className' => $classes ) );
}

function bf_hero( $slides, $media, $services_path ) {
	$markup = '';
	$dots = '';
	foreach ( $slides as $index => $slide ) {
		$image = bf_image( bf_image_id( $slide['image'], $media ), '', 'bf-hero-bgImg' );
		$bg = bf_group( 'bf-hero-bgLayer', bf_group( 'bf-hero-bgSlide', $image ) );
		$path = '/contact' === $slide['cta']['href'] ? bf_quote_path() : $slide['cta']['href'];
		$cta = bf_button( $slide['cta']['label'], $path, 'primary', 'lg' )
			. bf_button( 'Our Services', $services_path, 'lightOutline', 'lg' );
		$copy = bf_heading( $slide['headline'], 'bf-hero-headline', 0 === $index ? 1 : 2 )
			. bf_text( esc_html( $slide['subheadline'] ), 'bf-hero-subheadline' )
			. bf_buttons( 'bf-hero-ctaRow', $cta );
		$markup .= bf_group( 'bf-hero-slide', $bg . bf_html( '<div class="bf-hero-overlay" aria-hidden="true"></div>' )
			. bf_group( 'bf-hero-content', bf_group( 'bf-hero-contentInner', $copy ) ) );
		$number = $index + 1;
		$current = 0 === $index ? 'true' : 'false';
		$dots .= '<button type="button" class="bf-hero-dot" data-slide="' . $index . '" aria-current="' . $current . '" aria-label="Go to slide ' . $number . '"></button>';
	}
	$controls = '<button type="button" class="bf-hero-prevBtn" data-carousel-prev aria-label="Previous slide">' . blackfin_icon( 'left', 20 )
		. '</button><button type="button" class="bf-hero-nextBtn" data-carousel-next aria-label="Next slide">' . blackfin_icon( 'right', 20 )
		. '</button><div class="bf-hero-dots">' . $dots
		. '<button type="button" class="bf-hero-pause" data-carousel-pause aria-pressed="false" aria-label="Pause automatic slides">'
		. blackfin_icon( 'pause', 14 ) . blackfin_icon( 'play', 14 ) . '</button></div>';
	return bf_group( 'bf-hero-section', $markup . bf_html( '<div class="bf-hero-controls" aria-label="Slideshow controls" role="group">' . $controls . '</div>' ), 'section' );
}

function bf_service_lists( $name, $services ) {
	$prefix = 'residential' === $name ? 'res' : 'commercial';
	$cards = '';
	foreach ( $services as $index => $service ) {
		$title = 'res' === $prefix
			? ( 0 === $index ? 'bf-res-groupTitle' : 'bf-res-groupTitleSecondary' )
			: 'bf-commercial-cardTitle';
		$classes = 'res' === $prefix ? 'bf-res-card bf-res-card-' . $index : 'bf-commercial-card';
		$cards .= bf_group( $classes, bf_heading( $service['category'], $title, 'res' === $prefix ? 2 : 3 )
			. bf_list( $service['items'], "bf-$prefix-list" ) );
	}
	$header = 'commercial' === $name ? bf_group( 'bf-commercial-header',
		bf_text( 'Commercial Services', 'bf-commercial-tag' ) . bf_heading( 'Full-Scope Commercial Solutions', 'bf-commercial-title' ) ) : '';
	$anchor = 'services';
	return bf_group( "bf-$prefix-servicesSection", bf_group( 'container', $header
		. bf_group( "bf-$prefix-grid", $cards ) ), 'section', $anchor );
}

function bf_feature( $feature, $media ) {
	$mode = $feature['dark'] ? 'Dark' : 'Light';
	$image = bf_image( bf_image_id( $feature['image'], $media ), $feature['headline'], 'bf-feature-imageBg' );
	$left = bf_group( 'bf-feature-imageWrap', bf_group( 'bf-feature-imageParallax', $image ) );
	$right = bf_text( esc_html( $feature['tag'] ), "bf-feature-tag bf-feature-tag$mode" )
		. bf_heading( $feature['headline'], "bf-feature-headline bf-feature-headline$mode" )
		. bf_text( esc_html( $feature['body'] ), "bf-feature-body bf-feature-body$mode" )
		. bf_list( $feature['list'], "bf-feature-list bf-feature-listText$mode" )
		. bf_buttons( 'bf-feature-cta', bf_button( $feature['cta']['label'], '/contact' === $feature['cta']['href'] ? bf_quote_path() : $feature['cta']['href'],
			$feature['dark'] ? 'tealOutline' : 'primary' ) );
	$inner = 'bf-feature-inner' . ( $feature['reverse'] ? ' bf-feature-innerReverse' : '' );
	return bf_group( 'bf-feature-section bf-feature-' . strtolower( $mode ),
		bf_group( $inner, $left . bf_group( 'bf-feature-textWrap', $right ) ), 'section' );
}

function bf_gallery( $page, $media, $featured = false ) {
	$heading = $page['galleryTag'] ? bf_text( esc_html( $page['galleryTag'] ), 'bf-gallery-tag' ) : '';
	$heading .= bf_heading( $page['galleryTitle'], 'bf-gallery-title' );
	$photos = '';
	foreach ( $page['gallery'] as $photo ) {
		$photos .= bf_group( 'bf-gallery-item', bf_image( bf_image_id( $photo['src'], $media ), $photo['alt'], 'bf-gallery-img', 'large', $photo['caption'] ) );
	}
	return bf_group( $featured ? 'bf-gallery-section bf-gallery-featured' : 'bf-gallery-section', bf_group( 'container',
		bf_group( 'bf-gallery-header', $heading ) . bf_group( 'bf-gallery-grid', $photos ) ), 'section' );
}

function bf_contact_hero() {
	return bf_group( 'bf-contact-pageHero',
		bf_heading( 'Contact Us', 'bf-contact-heroTitle', 1 ) );
}

function bf_quote_section() {
	$heading = bf_heading( 'Request a Free Quote', 'bf-quote-title' )
		. bf_text( 'Prefer to call? ' . bf_link( 'tel:+17273306041', '(727) 330-6041', 'bf-quote-phone' ), 'bf-quote-intro' );
	$cards = '';
	$details = array(
		array( 'mail', 'Email', 'info@blackfincon.com', 'mailto:info@blackfincon.com' ),
		array( 'pin', 'Service Area', 'Entire Tampa Bay Region', '' ),
	);
	foreach ( $details as list( $icon, $label, $value, $url ) ) {
		$copy = bf_text( $label, 'bf-quote-infoLabel' )
			. bf_text( $url ? bf_link( $url, $value, 'bf-quote-infoLink' ) : esc_html( $value ), 'bf-quote-infoValue' );
		$cards .= bf_group( 'bf-quote-infoCard', bf_html( '<div class="bf-quote-infoIcon">' . blackfin_icon( $icon, 20 ) . '</div>' ) . bf_group( 'bf-quote-infoCopy', $copy ) );
	}
	$success = '<div class="bf-quote-success" role="status" hidden><span class="bf-quote-successIcon">' . blackfin_icon( 'check', 48 )
		. '</span><h3 class="bf-quote-successTitle">Message Received!</h3><p class="bf-quote-successText">Thank you for reaching out. We’ll get back to you shortly with your free quote.</p>'
		. '<button type="button" class="bf-button-btn bf-button-outline bf-button-md" data-quote-reset>Send Another</button></div>';
	$form = bf_block( 'shortcode', '[blackfin_quote_form]' ) . bf_html( $success );
	return bf_group( 'bf-quote-section', bf_group( 'container', bf_group( 'bf-quote-header', $heading )
		. bf_group( 'bf-quote-layout', bf_group( 'bf-quote-formPanel', $form ) . bf_group( 'bf-quote-infoCards', $cards ) ) ), 'section', 'request-a-quote' );
}

