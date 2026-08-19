<?php

/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/up-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

if ($upsells): ?>

	<div class="up-sells upsells products">
		<div class="products-section-header">
			<?php
			$heading = apply_filters('woocommerce_product_upsells_products_heading', __('You may also like', 'woocommerce'));

			if ($heading):
				?>
				<h2><?php echo esc_html($heading); ?></h2>
			<?php endif; ?>

			<div class="swiper-buttons-wrapper">
				<div class="swiper-buttons">
					<div class="swiper-button upsell-swiper-button-prev" tabindex="-1" role="button"
						aria-label="Previous slide">
						<i class="fa-solid fa-arrow-left"></i>
						<span class="nav-button-text">Prev</span>
					</div>
					<div class="swiper-button upsell-swiper-button-next" tabindex="0" role="button" aria-label="Next slide">
						<i class="fa-solid fa-arrow-right"></i>
						<span class="nav-button-text">Next</span>
					</div>
				</div>
			</div>
		</div>

		<?php //woocommerce_product_loop_start(); ?>
		<div class="swiper up-sells-swiper">
			<div class="swiper-wrapper">
				<?php foreach ($upsells as $upsell): ?>

					<?php
					$post_object = get_post($upsell->get_id());

					setup_postdata($GLOBALS['post'] = &$post_object); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
			
					wc_get_template_part('content', 'product');
					?>

				<?php endforeach; ?>
			</div>

		</div>
		<?php //woocommerce_product_loop_end(); ?>
	</div>
	<?php
endif;

wp_reset_postdata();
