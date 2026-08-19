<?php

/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 6.1.0
 */

defined('ABSPATH') || exit;

global $product;
$coupon_posts = get_posts(array(
	'posts_per_page' => 10, // Only show one for simplicity as per image
	'orderby' => 'name',
	'order' => 'asc',
	'post_type' => 'shop_coupon',
	'post_status' => 'publish',
));
$attribute_keys = array_keys($attributes);
$variations_json = wp_json_encode($available_variations);
$variations_attr = function_exists('wc_esc_json') ? wc_esc_json($variations_json) : _wp_specialchars($variations_json, ENT_QUOTES, 'UTF-8', true);

do_action('woocommerce_before_add_to_cart_form'); ?>

<form class="variations_form cart"
	action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>"
	method="post" enctype='multipart/form-data' data-product_id="<?php echo absint($product->get_id()); ?>"
	data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. 
	?>">
	<?php do_action('woocommerce_before_variations_form'); ?>

	<?php if (empty($available_variations) && false !== $available_variations): ?>
		<p class="stock out-of-stock">
			<?php echo esc_html(apply_filters('woocommerce_out_of_stock_message', __('This product is currently out of stock and unavailable.', 'woocommerce'))); ?>
		</p>
	<?php else: ?>
		<table class="variations" cellspacing="0" role="presentation">
			<tbody>
				<?php foreach ($attributes as $attribute_name => $options): ?>
					<tr>
						<th class="label">
							<div class="attribute-label-wrapper">
								<label for="<?php echo esc_attr(sanitize_title($attribute_name)); ?>"><?php echo wc_attribute_label($attribute_name); // WPCS: XSS ok. 
								   		?>
								</label>
								<?php if (str_contains($attribute_name, 'size')): ?>
									<p class="product-size-guide">
										<a data-bs-toggle="modal" data-bs-target="#exampleModal" class="size-guide-button">Size
											Chart</a>
									</p>
								<?php endif; ?>
							</div>

							<?php if (str_contains($attribute_name, 'size')): ?>
								<div class="modal fade " id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
									aria-hidden="true">
									<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="btn-close" data-bs-dismiss="modal"
													aria-label="Close"></button>
											</div>
											<div class="modal-body">
												<div class="rich-text rich-text__full-width">
													<h2>
														Find my ring size
													</h2>
													<h4>Method 1: Measure a Ring</h4>
													<div class="embedded-block-entry">
														<img src="//images.ctfassets.net/7m8i36sp5l90/1wZ32PHhpJ4QLg1ZQMJRc9/aa5f8e838005fd51fbfe6d52fe8d9c5f/Ring_icon__1_.svg"
															alt="Icon - Ring Size Guide Modal- Ring" class="icon">
													</div>
													<ol>
														<li>
															<p>Find a ring that fits comfortably on the intended finger.</p>
														</li>
														<li>
															<p>Measure the internal diameter (mm) of the ring, using a ruler or
																measuring tape.</p>
														</li>
														<li>
															<p>Find your size using our size chart above.</p>
														</li>
													</ol>
													<hr>
													<h4>Method 2: Measure your Finger</h4>
													<div class="embedded-block-entry">
														<img src="//images.ctfassets.net/7m8i36sp5l90/2nHdGViM84r1TsayihEynS/0b0e0cfc2aab9f5a669a3b2ddf045969/Hand_icon__1_.svg"
															alt="Icon  - Ring Size Guide Modal - Finger" class="icon">
													</div>
													<ol>
														<li>
															<p>Cut a piece string to a length of 10cm.</p>
														</li>
														<li>
															<p>Wrap the string comfortably around your finger where the ring should
																sit - this should not be pulled tight.</p>
														</li>
														<li>
															<p>Use a pen to mark the overlapping parts of the string. </p>
														</li>
														<li>
															<p>Lay the string out flat and measure the string between the two marked
																points (mm).</p>
														</li>
														<li>
															<p>Find your size using our size chart above.</p>
														</li>
													</ol>
													<hr>
													<h4>Expert tips</h4>
													<ol>
														<li>
															<p>Measure the correct finger - the same finger on the opposite hand
																will often be a different size.</p>
														</li>
														<li>
															<p>Don’t have string? A folded strip of paper will suffice.</p>
														</li>
														<li>
															<p>Always measure twice.</p>
														</li>
														<li>
															<p>Measuring in the afternoon when your hands are warm will give you the
																most accurate result.</p>
														</li>
														<li>
															<p>Oversized rings may require a larger size to ensure correct fitment.
																<span class="inline-entry__link"><a data-v-30f96398=""
																		id="2DgVgq4ztRdPSAC5y2oIaK" href="/contact-us"
																		target="_self" draggable="true" tabindex="0"
																		class="baseLink">Contact Customer Service</a></span> if
																you’re unsure.
															</p>
														</li>
													</ol>
													<p>Don’t worry about choosing the wrong size. All rings can be returned (except
														resized or made to order rings). See our <a id="RichText--13-1"
															href="https://ahnira.in/refund_returns/" target="_self" draggable="true"
															tabindex="0" class="baseLink">returns policy here.</a>
													</p>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</th>
						<td class="value">
							<?php
							wc_dropdown_variation_attribute_options(
								array(
									'options' => $options,
									'attribute' => $attribute_name,
									'product' => $product,
								)
							);
							//echo end($attribute_keys) === $attribute_name ? wp_kses_post(apply_filters('woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__('Clear', 'woocommerce') . '</a>')) : '';
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		if (!empty(($coupon_posts))): ?>
			<div class="premium-price-container mt-4">
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
									<div class="coupon-title"> <?php echo esc_html(strtoupper($c->code)); ?></div>
									<div class="coupon-desc">
										<?php echo $c->get_description() ?: 'Special Offer Available'; ?>.
										Use code: <strong><?php echo strtoupper($c->code); ?></strong>
									</div>
								</div>
								<button type="button" class="mt-4 ms-auto"
									style="font-size: 10px; padding: 2px 8px;"
									onclick="navigator.clipboard.writeText('<?php echo esc_js(strtoupper($c->code)); ?>'); var btn = this; btn.innerText = 'COPIED!'; setTimeout(function() { btn.innerText = 'COPY'; }, 2000);">COPY</button>
							</div>
							<?php
						endforeach;
					endif;
					?>
				</div>
			</div>
		<?php endif; ?>
		<?php do_action('woocommerce_after_variations_table'); ?>

		<div class="single_variation_wrap">
			<?php
			/**
			 * Hook: woocommerce_before_single_variation.
			 */
			do_action('woocommerce_before_single_variation');

			/**
			 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
			 *
			 * @since 2.4.0
			 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
			 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
			 */
			do_action('woocommerce_single_variation');

			/**
			 * Hook: woocommerce_after_single_variation.
			 */
			do_action('woocommerce_after_single_variation');
			?>
		</div>
	<?php endif; ?>

	<?php do_action('woocommerce_after_variations_form'); ?>
</form>

<?php
do_action('woocommerce_after_add_to_cart_form');
