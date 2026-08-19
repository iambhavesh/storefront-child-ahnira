<?php

/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;
$id = $product->get_id();
$link = $product->get_permalink();
// Main thumbnail.
$main_image_id     = get_post_thumbnail_id($id);
$main_alt          = get_post_meta($main_image_id, '_wp_attachment_image_alt', true);
$main_mobile_url   = wp_get_attachment_image_url($main_image_id, 'medium');
$main_desktop_url  = wp_get_attachment_image_url($main_image_id, 'medium_large');

// Hover thumbnail (first gallery image, falling back to main image).
$gallery_image_ids = $product->get_gallery_image_ids();
$hover_image_id     = ! empty($gallery_image_ids) ? $gallery_image_ids[0] : $main_image_id;
$hover_alt          = get_post_meta($hover_image_id, '_wp_attachment_image_alt', true);
$hover_mobile_url   = wp_get_attachment_image_url($hover_image_id, 'medium');
$hover_desktop_url  = wp_get_attachment_image_url($hover_image_id, 'medium_large');
// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
	return;
}
if (is_product() || is_cart()) { ?>
	<div <?php wc_product_class('col-md-4 col-6 product-col swiper-slide swatches-inserted', $product); ?>>

	<?php } else { ?>
		<div <?php wc_product_class('col-md-4 col-xl-3 col-6 product-col-wrapper swatches-inserted', $product); ?>>
		<?php }
		?>
		<div class="product-wrapper">
			<div class="product-thumbnail">
				<div class="onsale-label">
					<?php echo woocommerce_show_product_sale_flash();
					?>
				</div>
				<?php if (!$product->is_in_stock()) {
				?>
					<div class="out-of-stock-wrapper">
						<div class="out-of-stock-top">
							<span>Sold out</span>
						</div>
					</div>
				<?php } ?>
				<div class="thumbnail-wrapper">
					<?php
					/**
					 * Hook: woocommerce_before_shop_loop_item.
					 *
					 * @hooked woocommerce_template_loop_product_link_open - 10
					 */
					?>
					<div class="quick-view-wrapper">
						<?php echo do_shortcode("[ti_wishlists_addtowishlist loop=yes]"); ?>
						<?php //echo do_shortcode('[woosq id="{' . $id . '}"]'); 
						?>
					</div>
					<?php
					do_action('woocommerce_before_shop_loop_item');

					?>
					<div class="product-main-thumbnail">

						<img
							src="<?php echo esc_url(ahnira_compressed_image_url($main_image_id)); ?>"
							srcset="<?php echo esc_attr(ahnira_compressed_srcset($main_image_id)); ?>"
							sizes="(max-width: 768px) 50vw, 300px"
							alt="<?php echo esc_attr($main_alt); ?>"
							width="2560"
							height="2560"
							loading="lazy"
							decoding="async">

					</div>

					<div class="product-hover-thumbnail">

						<img
							src="<?php echo esc_url(ahnira_compressed_image_url($hover_image_id)); ?>"
							srcset="<?php echo esc_attr(ahnira_compressed_srcset($hover_image_id)); ?>"
							sizes="(max-width: 768px) 50vw, 300px"
							alt="<?php echo esc_attr($hover_alt); ?>"
							width="2560"
							height="2560"
							loading="lazy"
							decoding="async">

					</div>
					<?php
					$review_count = $product->get_review_count();
					if ($review_count > 0):
						$avg_rating = number_format((float) $product->get_average_rating(), 1);
					?>
						<div class="product-review-badge">
							<span class="review-badge-inner">
								<?php echo esc_html($avg_rating); ?>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
									class="review-star-icon" aria-hidden="true">
									<path
										d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
								</svg>
							</span>
						</div>
					<?php endif; ?>
					<div class="product-action">
						<?php woocommerce_template_loop_add_to_cart(); ?>
					</div>
				</div>
			</div>
			<div class="product-info">
				<a href="<?php echo esc_url($link) ?>">
					<div class="price">
						<?php
						echo woocommerce_template_loop_price();
						?>
					</div>
					<h3 class="product-title"><?php echo get_the_title() ?></h3>
					<?php
					remove_action(
						'woocommerce_after_shop_loop_item_title',
						'woocommerce_template_loop_rating',
						5
					);

					remove_action(
						'woocommerce_after_shop_loop_item_title',
						'woocommerce_show_product_loop_sale_flash',
						6
					);

					remove_action(
						'woocommerce_after_shop_loop_item_title',
						'woocommerce_template_loop_price',
						10
					);

					do_action('woocommerce_after_shop_loop_item_title');
					// do_action('woocommerce_after_shop_loop_item_title'); 
					?>
					<?php //echo do_shortcode('[display_attributes id="' . $id . '"]'); 
					?>
					<div class="add-to-cart-cta">
						<?php woocommerce_template_loop_add_to_cart(); ?>
					</div>
				</a>
			</div>
		</div>
		</div>