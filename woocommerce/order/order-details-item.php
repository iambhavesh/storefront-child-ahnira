<?php
/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!apply_filters('woocommerce_order_item_visible', true, $item)) {
	return;
}
?>
<div
	class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'woocommerce-table__line-item order_item d-flex justify-content-between mb-2', $item, $order)); ?>">

	<div class="woocommerce-table__product-name product-name">
		<?php
		$is_visible = $product && $product->is_visible();
		$product_permalink = apply_filters('woocommerce_order_item_permalink', $is_visible ? $product->get_permalink($item) : '', $item, $order);

		echo wp_kses_post(apply_filters('woocommerce_order_item_name', $product_permalink ? sprintf('<a href="%s">%s</a>', $product_permalink, $item->get_name()) : $item->get_name(), $item, $is_visible));

		$qty = $item->get_quantity();
		$refunded_qty = $order->get_qty_refunded_for_item($item_id);

		if ($refunded_qty) {
			$qty_display = '<del>' . esc_html($qty) . '</del> <ins>' . esc_html($qty - ($refunded_qty * -1)) . '</ins>';
		} else {
			$qty_display = esc_html($qty);
		}

		echo apply_filters('woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', $qty_display) . '</strong>', $item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		
		do_action('woocommerce_order_item_meta_start', $item_id, $item, $order, false);

		wc_display_item_meta($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		
		do_action('woocommerce_order_item_meta_end', $item_id, $item, $order, false);
		?>
	</div>

	<div class="woocommerce-table__product-total product-total text-end d-flex align-items-center justify-content-end">
		<?php 
			// Determine whether to show with or without tax based on WooCommerce settings
			$tax_display = get_option( 'woocommerce_tax_display_cart' );
			$subtotal_amount = 'excl' === $tax_display ? $order->get_line_subtotal( $item ) : $order->get_line_subtotal( $item, true );
			
			// Format the base subtotal price
			$subtotal_html = wc_price( $subtotal_amount, array( 'currency' => $order->get_currency() ) );
			echo $subtotal_html;
			
			// Build the tax suffix manually if there are taxes applied
			$taxes = $item->get_taxes();
			$tax_strings = array();
			
			if ( ! empty( $taxes['subtotal'] ) ) {
				foreach ( $taxes['subtotal'] as $tax_rate_id => $tax_amount ) {
					if ( $tax_amount > 0 ) {
						// Retrieve the tax label (e.g., CGST, SGST)
						$tax_label = WC_Tax::get_rate_label( $tax_rate_id );
						$raw_tax_price = strip_tags( wc_price( $tax_amount, array( 'currency' => $order->get_currency() ) ) );
						$tax_strings[] = $raw_tax_price . ' ' . $tax_label;
					}
				}
			}
			
			if ( ! empty( $tax_strings ) ) {
				echo '<span class="includes_tax text-muted ms-2 text-nowrap" style="font-size: 0.85em;">(includes ' . implode( ', ', $tax_strings ) . ')</span>';
			}
		?>
	</div>

</div>

<?php if ($show_purchase_note && $purchase_note): ?>

	<div class="woocommerce-table__product-purchase-note product-purchase-note">

		<div class="w-100">
			<?php echo wpautop(do_shortcode(wp_kses_post($purchase_note))); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>

	</div>

<?php endif; ?>