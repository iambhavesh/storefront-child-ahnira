<?php

/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */
error_log('AHNIRA CART LOADED: ' . __FILE__);


$free_shipping_limit = get_option('free_shipping_limit', 0);
$cart_subtotal = WC()->cart->get_subtotal();
$remaining = $free_shipping_limit - $cart_subtotal;
$percentage = ($free_shipping_limit > 0) ? ($cart_subtotal / $free_shipping_limit) * 100 : 0;
if ($percentage > 100)
	$percentage = 100;

?>
<div class="cart-page-wrapper py-4">
	<div class="container">

		<!-- Checkout Progress Bar -->
		<!-- <div class="checkout-steps d-flex justify-content-center align-items-center mb-5 mt-2">
			<div class="step active d-flex align-items-center">
				<span class="step-num">1</span> <span class="step-text fw-bold">Cart</span>
			</div>
			<div class="step-line mx-4"></div>
			<div class="step d-flex align-items-center">
				<span class="step-num">2</span> <span class="step-text">Address</span>
			</div>
			<div class="step-line mx-4"></div>
			<div class="step d-flex align-items-center">
				<span class="step-num">3</span> <span class="step-text">Payment</span>
			</div>
		</div> -->

		<?php do_action('woocommerce_before_cart'); ?>

		<!-- Demand Notice -->
		<div class="demand-alert mb-4 p-3 d-flex align-items-center justify-content-center rounded">
			<i class="fa-solid fa-bolt me-2 ahnira-text-warning"></i>
			<span>Items in your cart are in high demand. Checkout now to secure your order!</span>
		</div>

		<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
			<div class="row gx-lg-5">
				<div class="col-12 col-md-8">

					<!-- Free Shipping Bar -->
					<?php if ($free_shipping_limit > 0): ?>
						<div class="shipping-progress-wrapper bg-white p-4 border rounded mb-4 shadow-sm">
							<div class="d-flex justify-content-between mb-2">
								<span class="shipping-msg">
									<?php if ($remaining > 0): ?>
										Add <strong><?php echo wc_price($remaining); ?></strong> more for <strong>FREE
											SHIPPING</strong>
									<?php else: ?>
										You've unlocked <strong>FREE SHIPPING</strong>!
									<?php endif; ?>
								</span>
								<span class="shipping-perc text-muted small"><?php echo round($percentage); ?>%
									complete</span>
							</div>
							<div class="progress" style="height: 6px;">
								<div class="progress-bar ahnira-progress-bar" role="progressbar"
									style="width: <?php echo $percentage; ?>%;" aria-valuenow="<?php echo $percentage; ?>"
									aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
					<?php endif; ?>

					<?php do_action('woocommerce_before_cart_table'); ?>

					<div class="cart-items-container bg-white">
						<?php do_action('woocommerce_before_cart_contents'); ?>

						<?php
						foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
							$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
							$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
							$product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);

							if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
								$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);

								// Calculation of Savings
								$regular_price = $_product->get_regular_price();
								$sale_price = $_product->get_sale_price();
								$savings = 0;
								if ($sale_price && $regular_price > $sale_price) {
									$savings = ($regular_price - $sale_price);
								}

								// Get Material from specs
								$specs = get_post_meta($product_id, '_product_specifications', true);
								$material = '';
								if (!empty($specs) && is_array($specs)) {
									foreach ($specs as $spec) {
										if (strtolower($spec['label']) == 'material') {
											$material = $spec['value'];
											break;
										}
									}
								}
								?>
								<div
									class="cart-item-row p-4 border-bottom <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
									<div class="row align-items-start">
										<div class="col-3">
											<div class="product-thumbnail border rounded overflow-hidden">
												<?php
												$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
												if (!$product_permalink) {
													echo $thumbnail;
												} else {
													printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
												}
												?>
											</div>
										</div>
										<div class="col-9">
											<div class="d-flex justify-content-between row-info align-items-start">
												<div class="product-info flex-grow-1">
													<div class="d-flex align-items-center mb-1">
														<h5 class=" ahnira-fw-bold product-title  mb-0 me-2">
															<?php
															if (!$product_permalink) {
																echo wp_kses_post($product_name);
															} else {
																echo wp_kses_post(sprintf('<a href="%s" class="ahnira-text-secondary ahnira-fw-bold ahnira-text-decoration-none">%s</a>', esc_url($product_permalink), $product_name));
															}
															?>
														</h5>
														<?php if ($savings > 0): ?>
															<?php $total_saving = ($regular_price * $cart_item['quantity'] - $sale_price * $cart_item['quantity']); ?>
															<span
																class="badge-savings px-2 py-0 rounded ahnira-bg-light-teal ahnira-text-teal"
																style="text-transform:uppercase;"><?php echo "SAVE" . get_woocommerce_currency_symbol() . ($total_saving); ?></span>
														<?php endif; ?>
													</div>

													<?php if ($material): ?>
														<p class="product-meta text-muted small mb-3">Material:
															<?php echo esc_html($material); ?>
														</p>
													<?php endif; ?>

													<?php echo wc_get_formatted_cart_item_data($cart_item); ?>

													<div class="cart-item-controls d-flex align-items-center mt-3">
														<div
															class="quantity-input-wrapper d-flex align-items-center border rounded bg-white">
															<?php
															if ($_product->is_sold_individually()) {
																$product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
															} else {
																$product_quantity = woocommerce_quantity_input(
																	array(
																		'input_name' => "cart[{$cart_item_key}][qty]",
																		'input_value' => $cart_item['quantity'],
																		'max_value' => $_product->get_max_purchase_quantity(),
																		'min_value' => '0',
																		'product_name' => $product_name,
																	),
																	$_product,
																	false
																);
															}
															echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
															?>
														</div>
													</div>
												</div>

												<div class="product-price text-end ms-4">
													<!-- <div class="current-price ahnira-fw-bold">
														<?php //echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
													</div> -->
													<?php if (empty($sale_price)) { ?>
														<div class="current-price ahnira-fw-bold">
															<?php echo wc_price((float) $regular_price * (int) $cart_item['quantity']); ?>
														</div>
													<?php } else { ?>
														<div class="current-price ahnira-fw-bold">
															<?php echo wc_price((float) $sale_price * (int) $cart_item['quantity']); ?>
														</div>

														<div
															class="old-price ahnira-text-muted text-decoration-line-through xsmall">
															<span class="regular-price small">
																<?php echo get_woocommerce_currency_symbol() . ($regular_price * $cart_item['quantity']); ?>
															</span>
														</div>
													<?php } ?>
													<div class="item-actions mt-0 mt-xl-3 d-flex gap-3 small">
														<a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>"
															class="ahnira-text-muted d-flex align-items-center remove-link"
															aria-label="Remove item">
															<i class="fa-regular fa-trash-can me-1"></i> Remove
														</a>
														<!-- <a href="<?php // echo $product_permalink ? esc_url($product_permalink) : '#'; ?>"
															class="ahnira-text-muted d-flex align-items-center wishlist-link"
															data-product-id="<?php //echo esc_attr($product_id); ?>"
															data-variation-id="<?php // echo esc_attr(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : 0); ?>"
															data-product-type="<?php //echo esc_attr($_product->get_type()); ?>"
															aria-label="Add to wishlist">
															<i class="fa-regular fa-heart me-1"></i> Wishlist
														</a> -->
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php
							}
						}
						?>

						<?php do_action('woocommerce_cart_contents'); ?>

						<div class="actions d-none">
							<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
							<button type="submit" class="button update-cart" name="update_cart"
								value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>
							<?php do_action('woocommerce_cart_actions'); ?>
						</div>

						<?php do_action('woocommerce_after_cart_contents'); ?>
					</div>
					<?php do_action('woocommerce_after_cart_table'); ?>
				</div>

				<div class="col-12 col-md-4 mt-5 mt-md-0">
					<div class="cart-sidebar">
						<!-- Apply Coupon Section -->

						<?php echo ahnira_cart_coupon_section_html(); ?>


						<div class="ahnira-cart-notice-container"></div>
						<?php do_action('woocommerce_before_cart_collaterals'); ?>
						<?php woocommerce_cart_totals(); ?>
					</div>
				</div>
			</div>

			<!-- Cross Sells -->
			<div class="row mt-5">
				<div class="col-12">
					<div class="cross-sells-container">
						<?php woocommerce_cross_sell_display(); ?>
					</div>
				</div>
			</div>
		</form>
		<?php do_action('woocommerce_after_cart'); ?>
	</div>
</div>