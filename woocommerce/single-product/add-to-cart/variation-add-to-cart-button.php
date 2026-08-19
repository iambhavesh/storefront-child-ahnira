<?php

/**
 * Single variation cart button
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

global $product;
?>
<div class="woocommerce-variation-add-to-cart variations_button">
	<div class="quantity-wrapper">
		<?php do_action('woocommerce_before_add_to_cart_button'); ?>

		<?php
		do_action('woocommerce_before_add_to_cart_quantity');

		// woocommerce_quantity_input(
		// 	array(
		// 		'min_value' => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
		// 		'max_value' => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
		// 		'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
		// 	)
		// );
		
		do_action('woocommerce_after_add_to_cart_quantity');
		?>
		<div class="row g-2 w-100 mobile-fixed-bootom">
			<input type="hidden" name="quantity" class="qty" value="1" min="1" />
			<div class="col-5">
				<button type="submit"
					class="single_add_to_cart_button button product-button w-100 <?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"><i class="fa-solid fa-bag-shopping"></i><?php echo esc_html($product->single_add_to_cart_text()); ?></button>
			</div>
			<div class="col-7">
				<?php do_action('woocommerce_after_add_to_cart_button'); ?>
			</div>
		</div>

		<input type="hidden" name="add-to-cart" value="<?php echo absint($product->get_id()); ?>" />
		<input type="hidden" name="product_id" value="<?php echo absint($product->get_id()); ?>" />
		<input type="hidden" name="variation_id" class="variation_id" value="0" />
	</div>
</div>