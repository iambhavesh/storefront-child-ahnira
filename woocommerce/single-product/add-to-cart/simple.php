<?php

/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

global $product;

if (!$product->is_purchasable()) {
	return;
}
$coupon_posts = get_posts(array(
	'posts_per_page' => 10, // Only show one for simplicity as per image
	'orderby' => 'name',
	'order' => 'asc',
	'post_type' => 'shop_coupon',
	'post_status' => 'publish',
));
echo wc_get_stock_html($product); // WPCS: XSS ok.

if ($product->is_in_stock()): ?>

	<?php do_action('woocommerce_before_add_to_cart_form'); ?>

	<form class="cart"
		action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>"
		method="post" enctype='multipart/form-data'>
		<?php
		if (!empty(($coupon_posts))): ?>
			<div class="premium-price-container">
				<?php if ($product->is_on_sale()): ?>
					<div class="flash-sale-label">FLASH SALE</div>
				<?php endif; ?>

				<div class="price-row">
					<?php
					if (!empty($coupon_posts)):
						foreach ($coupon_posts as $coupon_post):
							$c = new WC_Coupon($coupon_post->post_name);
					?>
							<div class="coupon-offer-row">
								<div class="coupon-icon">
									<i class="fa-solid fa-tag"></i>
								</div>
								<div class="coupon-details">
									<div class="coupon-title">
										<?php echo esc_html(strtoupper($c->get_code())); ?>
									</div>
									<div class="coupon-desc">
										<?php echo $c->get_description() ?: 'Special Offer Available'; ?>.
										Use code: <strong>
											<?php echo strtoupper($c->get_code()); ?>
										</strong>
									</div>

								</div>
								<button type="button" class="mt-4 ms-auto"
									style="font-size: 10px; padding: 2px 8px;"
									onclick="navigator.clipboard.writeText('<?php echo esc_js(strtoupper($c->get_code())); ?>'); var btn = this; btn.innerText = 'COPIED!'; setTimeout(function() { btn.innerText = 'COPY'; }, 2000);">COPY</button>
							</div>
					<?php
						endforeach;
					endif;
					?>
				</div>
			</div>
		<?php endif; ?>
		<?php do_action('woocommerce_before_add_to_cart_button'); ?>
		<div class="quantity-wrapper">
			<?php
			do_action('woocommerce_before_add_to_cart_quantity');

			do_action('woocommerce_after_add_to_cart_quantity');
			?>
			<div class="row g-2 w-100 mobile-fixed-bootom">
				<input type="hidden" name="quantity" class="qty" value="1" min="1" />
				<div class="col-5">
					<button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>"
						class="single_add_to_cart_button button product-button w-100 <?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"><i class="fa-solid fa-bag-shopping"></i><?php echo esc_html($product->single_add_to_cart_text()); ?></button>
				</div>
				<div class="col-7">
					<?php do_action('woocommerce_after_add_to_cart_button'); ?>
				</div>
			</div>
		</div>
	</form>

	<?php do_action('woocommerce_after_add_to_cart_form'); ?>

<?php endif; ?>