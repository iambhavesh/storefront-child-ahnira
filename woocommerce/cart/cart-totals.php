<?php

/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined('ABSPATH') || exit;

$total_savings = 0;
foreach (WC()->cart->get_cart() as $cart_item) {
	$_product = $cart_item['data'];
	$regular_price = $_product->get_regular_price();
	$sale_price = $_product->get_sale_price();
	if ($sale_price && $regular_price > $sale_price) {
		$total_savings += ($regular_price - $sale_price) * $cart_item['quantity'];
	}
}

// Add coupon discounts to total savings
$total_savings += WC()->cart->get_discount_total();

?>
<div class="cart_totals bg-white p-4 <?php echo (WC()->customer->has_calculated_shipping()) ? 'calculated_shipping' : ''; ?>"
	id="cart-totals-section">

	<?php do_action('woocommerce_before_cart_totals'); ?>

	<h5 class="fw-bold mb-4"><?php esc_html_e('Order Summary', 'woocommerce'); ?></h5>

	<div class="order-summary-table">
		<div class="summary-row d-flex justify-content-between mb-3">
			<span class="text-muted"><?php esc_html_e('Subtotal', 'woocommerce'); ?></span>
			<span class="fw-bold"><?php wc_cart_totals_subtotal_html(); ?></span>
		</div>

		<?php foreach (WC()->cart->get_coupons() as $code => $coupon): ?>
			<div
				class="summary-row d-flex justify-content-between mb-3 coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
				<span class="text-muted">Coupon</span>
				<div class="text-end">
					<span class="fw-bold text-success me-2">
						<?php $amount = WC()->cart->get_coupon_discount_amount($code); ?>
						<span
							class="text-success"><?php echo get_woocommerce_currency_symbol() . number_format($amount, 2); ?></span>
						<a href="<?php echo esc_url(add_query_arg('remove_coupon', urlencode($code), wc_get_cart_url())); ?>"
							class="woocommerce-remove-coupon ms-2 text-decoration-none text-danger text-uppercase fw-bold"
							data-coupon="<?php echo esc_attr($code); ?>"><?php esc_html_e('REMOVE', 'woocommerce'); ?></a>
					</span>
				</div>
			</div>
		<?php endforeach; ?>

		<?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()): ?>
			<div class="summary-row d-flex justify-content-between mb-3 shipping-row">
				<span class="text-muted"><?php esc_html_e('Shipping', 'woocommerce'); ?></span>
				<div class="text-end">
					<?php
					$shipping_total = WC()->cart->get_shipping_total();
					if ($shipping_total == 0): ?>
						<span class="badge" style="background: #e6fffa; color: #38b2ac;">FREE</span>
					<?php else: ?>
						<span class="fw-bold"><?php echo wc_price($shipping_total); ?></span>
					<?php endif; ?>

					<?php if (is_cart() && WC()->cart->get_customer()->get_shipping_postcode()): ?>
						<p class="xsmall text-muted mb-0" style="font-size: 10px;">Shipping to
							<?php echo esc_html(WC()->cart->get_customer()->get_shipping_postcode()); ?>,
							<?php echo esc_html(WC()->cart->get_customer()->get_shipping_city()); ?></p>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php foreach (WC()->cart->get_fees() as $fee): ?>
			<div class="summary-row d-flex justify-content-between mb-3">
				<span class="text-muted"><?php echo esc_html($fee->name); ?></span>
				<span class="fw-bold"><?php wc_cart_totals_fee_html($fee); ?></span>
			</div>
		<?php endforeach; ?>

		<?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()): ?>
			<?php if ('itemized' === get_option('woocommerce_tax_total_display')): ?>
				<?php foreach (WC()->cart->get_tax_totals() as $code => $tax): ?>
					<div class="summary-row d-flex justify-content-between mb-3">
						<span class="text-muted"><?php echo esc_html($tax->label); ?></span>
						<span class="fw-bold"><?php echo wp_kses_post($tax->formatted_amount); ?></span>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<div class="summary-row d-flex justify-content-between mb-3">
					<span class="text-muted"><?php echo esc_html(WC()->countries->tax_or_vat()); ?></span>
					<span class="fw-bold"><?php wc_cart_totals_taxes_total_html(); ?></span>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<div class="border-top border-dashed my-4"></div>

		<div class="total-row d-flex justify-content-between align-items-center">
			<span class="fs-5 fw-bold" style="color: #333;"><?php esc_html_e('Total Amount', 'woocommerce'); ?></span>
			<div class="text-end">
				<div class="fs-3 fw-bold" style="color: #000;">
					<?php $cart_total = WC()->cart->total;
					echo get_woocommerce_currency_symbol() . number_format($cart_total, 2); ?>
				</div>
				<?php if ($total_savings > 0): ?>
					<div class="savings-msg small text-success fw-bold mt-1" style="font-size: 11px;">You Saved
						<?php echo get_woocommerce_currency_symbol() . number_format($total_savings, 2); ?> on this order!
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="wc-proceed-to-checkout mt-4">
			<div class="checkout-footer-wrapper">
			<div class="footer-total-summary d-md-none col-5">
				<div class="total-price   fw-bold fs-4">
					<?php $cart_total = WC()->cart->total;
					echo get_woocommerce_currency_symbol() . number_format($cart_total, 2); ?>
				</div>
				<a href="#cart-totals-section" class="view-summary-link text-primary small  col-md-12">
					<?php _e('View Order Summary', 'woocommerce'); ?>
				</a>
			</div>
			<div class="col-7 col-xl-12">
				<?php do_action('woocommerce_proceed_to_checkout'); ?>
			</div>

		</div>
	</div>

	<?php do_action('woocommerce_after_cart_totals'); ?>

</div>