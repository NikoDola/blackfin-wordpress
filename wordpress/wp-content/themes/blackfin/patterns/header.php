<?php
/**
 * Title: Blackfin header
 * Slug: blackfin/header
 * Inserter: no
 */
$menu = (int) get_option( 'blackfin_navigation_id' );
$quote = home_url( '/contact/#request-a-quote' );
$home_link = home_url( '/' );
?>
<!-- wp:group {"className":"bf-nav-header","layout":{"type":"default"}} -->
<div class="wp-block-group bf-nav-header">
	<!-- wp:group {"className":"bf-nav-inner","layout":{"type":"default"}} -->
	<div class="wp-block-group bf-nav-inner">
		<!-- wp:group {"className":"bf-nav-logo","layout":{"type":"default"}} -->
		<div class="wp-block-group bf-nav-logo">
			<!-- wp:site-logo {"width":50,"shouldSyncIcon":false,"className":"bf-nav-logoImage"} /-->
			<!-- wp:paragraph {"className":"bf-nav-logoText"} -->
			<p class="bf-nav-logoText"><a href="<?php echo esc_url( $home_link ); ?>"><strong class="bf-nav-logoName">Blackfin</strong><br><span class="bf-nav-logoSub">Construction &amp; Painting</span></a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- wp:html -->
		<button type="button" class="bf-nav-hamburger" data-menu-toggle aria-controls="blackfin-primary-nav" aria-expanded="false" aria-label="Open menu"><?php echo blackfin_icon( 'menu', 24 ) . blackfin_icon( 'close', 24 ); ?></button>
		<!-- /wp:html -->
		<!-- wp:group {"className":"bf-nav-menuWrap","anchor":"blackfin-primary-nav","layout":{"type":"default"}} -->
		<div id="blackfin-primary-nav" class="wp-block-group bf-nav-menuWrap">
			<!-- wp:navigation <?php echo serialize_block_attributes( array( 'ref' => $menu, 'overlayMenu' => 'never', 'className' => 'bf-menu bf-nav-desktopNav', 'ariaLabel' => 'Main menu' ) ); ?> /-->
			<!-- wp:html -->
			<div class="bf-nav-mobileActions"><a class="bf-nav-mobilePhone" href="tel:+17273306041"><?php echo blackfin_icon( 'phone', 16 ); ?> (727) 330-6041</a><a class="bf-button-btn bf-button-primary bf-button-sm bf-button-fullWidth" href="<?php echo esc_url( $quote ); ?>">Free Quote</a></div>
			<!-- /wp:html -->
		</div>
		<!-- /wp:group -->
		<!-- wp:html -->
		<div class="bf-nav-desktopActions"><a class="bf-nav-phoneLink" href="tel:+17273306041"><?php echo blackfin_icon( 'phone', 14 ); ?> (727) 330-6041</a><a class="bf-button-btn bf-button-primary bf-button-sm" href="<?php echo esc_url( $quote ); ?>">Free Quote</a></div>
		<!-- /wp:html -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

