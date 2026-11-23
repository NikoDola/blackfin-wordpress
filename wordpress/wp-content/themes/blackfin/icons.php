<?php
/** Lucide line icons used in the Blackfin reference. */

function blackfin_icon( $name, $size ) {
	$icons = array(
		'phone' => '<path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/>',
		'instagram' => '<rect width="20" height="20" x="2" y="2" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><path d="M17.5 6.5h.01"/>',
		'pin' => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>',
		'mail' => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
		'check' => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
		'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
		'close' => '<path d="m18 6-12 12M6 6l12 12"/>',
		'left' => '<path d="m15 18-6-6 6-6"/>',
		'right' => '<path d="m9 18 6-6-6-6"/>',
		'arrow' => '<path d="M5 12h14m-7-7 7 7-7 7"/>',
		'pause' => '<path d="M9 5v14m6-14v14"/>',
		'play' => '<path d="m9 5 12 7-12 7z"/>',
	);
	return sprintf( '<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%2$s</svg>', $size, $icons[ $name ] );
}

