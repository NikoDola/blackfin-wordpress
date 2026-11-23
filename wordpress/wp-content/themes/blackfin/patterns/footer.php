<?php
/**
 * Title: Blackfin footer
 * Slug: blackfin/footer
 * Inserter: no
 */
$menu = (int) get_option( 'blackfin_navigation_id' );
$locations = blackfin_service_areas();
?>
<!-- wp:group {"className":"bf-footer-footer","layout":{"type":"default"}} -->
<div class="wp-block-group bf-footer-footer">
	<!-- wp:group {"className":"bf-footer-inner","layout":{"type":"default"}} -->
	<div class="wp-block-group bf-footer-inner">
		<!-- wp:group {"className":"bf-footer-grid","layout":{"type":"default"}} -->
		<div class="wp-block-group bf-footer-grid">
			<!-- wp:group {"className":"bf-footer-brand","layout":{"type":"default"}} -->
			<div class="wp-block-group bf-footer-brand">
				<!-- wp:group {"className":"bf-footer-brandRow","layout":{"type":"default"}} -->
				<div class="wp-block-group bf-footer-brandRow">
					<!-- wp:site-logo {"width":52,"isLink":false,"shouldSyncIcon":false,"className":"bf-footer-logoImage"} /-->
					<!-- wp:paragraph --><p><strong class="bf-footer-brandName">Blackfin</strong><br><span class="bf-footer-brandSub">Construction &amp; Painting</span></p><!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
				<!-- wp:paragraph {"className":"bf-footer-brandDesc"} -->
				<p class="bf-footer-brandDesc">Tampa Bay's finest painting and remodeling contractor. Leverage our years of proven experience to make your project the best it can be.</p>
				<!-- /wp:paragraph -->
				<!-- wp:html -->
				<div class="bf-footer-socialRow">
					<a class="bf-footer-socialLink" href="https://www.instagram.com/blackfincon/" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><?php echo blackfin_icon( 'instagram', 20 ); ?></a>
					<a class="bf-footer-socialLink" href="tel:+17273306041"><?php echo blackfin_icon( 'phone', 16 ); ?> (727) 330-6041</a>
				</div>
				<!-- /wp:html -->
			</div>
			<!-- /wp:group -->
			<!-- wp:group {"className":"bf-footer-pages","layout":{"type":"default"}} -->
			<div class="wp-block-group bf-footer-pages">
				<!-- wp:heading {"level":3,"className":"bf-footer-colTitle"} --><h3 class="wp-block-heading bf-footer-colTitle">Pages</h3><!-- /wp:heading -->
				<!-- wp:navigation <?php echo serialize_block_attributes( array( 'ref' => $menu, 'overlayMenu' => 'never', 'className' => 'bf-menu bf-footer-linkList', 'layout' => array( 'type' => 'flex', 'orientation' => 'vertical' ), 'ariaLabel' => 'Footer menu' ) ); ?> /-->
				<!-- wp:paragraph {"className":"bf-footer-privacy"} -->
				<p class="bf-footer-privacy"><a class="bf-footer-footerLink" href="<?php echo esc_url( get_privacy_policy_url() ?: home_url( '/privacy-policy/' ) ); ?>">Privacy Policy</a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
			<!-- wp:group {"className":"bf-footer-areas","layout":{"type":"default"}} -->
			<div class="wp-block-group bf-footer-areas">
				<!-- wp:html --><h3 class="bf-footer-colTitle"><?php echo blackfin_icon( 'pin', 12 ); ?> Service Area</h3><!-- /wp:html -->
				<!-- wp:paragraph {"className":"bf-footer-serviceAreaDesc"} --><p class="bf-footer-serviceAreaDesc">Serving the Entire Tampa Bay Region</p><!-- /wp:paragraph -->
				<!-- wp:group {"className":"bf-footer-tagList","layout":{"type":"default"}} -->
				<div class="wp-block-group bf-footer-tagList">
					<?php foreach ( $locations as $location ) : ?>
						<!-- wp:paragraph {"className":"bf-footer-tag"} --><p class="bf-footer-tag"><?php echo esc_html( $location ); ?></p><!-- /wp:paragraph -->
					<?php endforeach; ?>
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
	<!-- wp:group {"className":"bf-footer-bottomBar","layout":{"type":"default"}} -->
	<div class="wp-block-group bf-footer-bottomBar">
		<!-- wp:group {"className":"bf-footer-bottomInner","layout":{"type":"default"}} -->
		<div class="wp-block-group bf-footer-bottomInner">
			<!-- wp:paragraph {"className":"bf-footer-copyright"} --><p class="bf-footer-copyright">© <?php echo esc_html( wp_date( 'Y' ) ); ?> Blackfin Construction &amp; Painting. All rights reserved.</p><!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"bf-footer-copyright"} --><p class="bf-footer-copyright">Tampa Bay, Florida</p><!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

