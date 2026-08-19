<?php

/**
 * Custom Single Product Template matching productpage.html
 */

defined('ABSPATH') || exit;

global $product;
$product_id = $product->get_id();
$gallery_image_ids = $product->get_gallery_image_ids();
$main_image_id = get_post_thumbnail_id($product_id);
$main_image_url = wp_get_attachment_image_url($main_image_id, 'full');
//$mobile_thumb_url    = wp_get_attachment_image_url($main_image_id, 'product-thumb-500');
$mobile_1x_url = wp_get_attachment_image_url($main_image_id, 'product-thumb-380');
$mobile_2x_url = wp_get_attachment_image_url($main_image_id, 'medium_large');
$desktop_thumb_url = wp_get_attachment_image_url($main_image_id, 'full');
if (is_a($product, 'WC_Product')) {
	// 2. Get the main image (featured image) attachment ID
	$image_id = $product->get_image_id();

	if ($image_id) {
		// 3. Fetch the alt tag meta string
		$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);

		// Fallback: If empty, use the product title
		if (empty($image_alt)) {
			$image_alt = $product->get_name();
		}

		//	echo esc_attr($image_alt);
	}
}
do_action('woocommerce_before_single_product');

if (post_password_required()) {
	echo get_the_password_form();
	return;
}
?>

<header class="woocommerce-products-header container d-flex">
	<div id="page-breadcrumb" class="page-breadcrumb ">
		<div class="container-wide">
			<div class="page-breadcrumb-wrap">
				<?php woocommerce_breadcrumb(); ?>
			</div>
		</div>
	</div>
</header>

<div class="container container-wide">
	<div id="product-<?php the_ID(); ?>" <?php wc_product_class('product-page', $product); ?>>

		<div class="row  px-lg-4">

			<!-- 1. Left: Image Gallery -->
			<div class="col-lg-6 " id="product-image-parent">
				<div class="single-image-gallery image-gallery-sticky d-flex flex-column">
					<!-- Bestseller Badge (Example: check if featured or has tag) -->
					<?php if ($product->is_featured()): ?>
						<!-- <div class="absolute top-4 left-4 z-10">
							<span
								class="bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider shadow-lg">Bestseller</span>
						</div> -->
					<?php endif; ?>
					<!-- Thumbnails Strip (plain divs – no Swiper required) -->
					<div class="thumbs-swiper-wrap d-flex">
						<div class="ahnira-thumbs-strip">

							<?php
							// Use WC-native get_image_id() for the main image (get_post_thumbnail_id can return 0 for WC products)
							$main_thumb_id   = $product->get_image_id();
							$all_thumb_ids   = array_merge(
								$main_thumb_id ? array($main_thumb_id) : array(),
								$gallery_image_ids
							);
							$thumb_index_out = 0;
							foreach ($all_thumb_ids as $thumb_id):
								if (! $thumb_id) continue; // skip any falsy IDs (wp_get_attachment_image(0) returns empty)
								$thumb_full_url  = wp_get_attachment_image_url($thumb_id, 'full');
								$is_active_thumb = (0 === $thumb_index_out);
							?>
								<div class="ahnira-thumb-slide <?php echo $is_active_thumb ? 'swiper-slide-thumb-active' : ''; ?>"
									data-index="<?php echo esc_attr($thumb_index_out); ?>"
									data-large-src="<?php echo esc_url($thumb_full_url); ?>"
									role="button"
									tabindex="0"
									aria-label="<?php printf(esc_attr__('View image %d', 'storefront'), $thumb_index_out + 1); ?>">
									<div class="swiper-thumbnail-wrap">
										<?php echo wp_get_attachment_image(
											$thumb_id,
											'thumbnail',
											false,
											array(
												'alt'           => 'thumbnail_images',
												'fetchpriority' => $is_active_thumb ? 'high' : 'auto',
												'loading'       => $is_active_thumb ? 'eager' : 'lazy',
											)
										); ?>
									</div>
								</div>
							<?php $thumb_index_out++;
							endforeach; ?>

						</div>
					</div>
					<!-- Main Slider -->
					<div class="main-swiper image-swiper">
						<div class="product-image-wishlist-btn">
							<?php echo do_shortcode('[ti_wishlists_addtowishlist]'); ?>
						</div>
						<?php
						woocommerce_show_product_images();
						?>
						<!-- Try On Floating Button -->
						<!-- <div class="try-on-btn-wrap"
							style="position: absolute; bottom: 20px; right: 20px; z-index: 10;">
							<button
								class="bg-white/90 backdrop-blur-sm text-black px-4 py-2 rounded-full border-0 d-flex align-items-center gap-2 text-sm font-bold shadow-lg hover:bg-white transition-all">
								<span class="material-symbols-outlined text-primary text-xl">view_in_ar</span> Try On
							</button>
						</div> -->
					</div>

				</div>
			</div>

			<!-- 2. Right: Product Summary -->
			<div class="col-lg-6">
				<div class="product-summary">
					<div class="price-row">
						<div class="main-price">
							<?php

							$on_sale = $product->is_on_sale();
							$regular_price = '';
							$sale_price = '';

							if ($product->is_type('variable')) {
								$prices = $product->get_variation_prices(true);
								$regular_price = current($prices['regular_price']);
								$sale_price = current($prices['sale_price']);
							} else {
								$regular_price = $product->get_regular_price();
								$sale_price = $product->get_sale_price();
							}

							echo woocommerce_template_loop_price();

							if ($on_sale && $regular_price && $sale_price && (float) $regular_price > (float) $sale_price):
								$percentage = round((((float) $regular_price - (float) $sale_price) / (float) $regular_price) * 100);
							?>
								<span class="discount-percentage">
									<?php echo $percentage; ?>% OFF
								</span>
							<?php endif; ?>
							<?php
							$average_rating = (float) $product->get_average_rating();
							$review_count = (int) $product->get_review_count();
							if ($average_rating > 0):
							?>
								<div class="inline-rating-badge d-flex align-items-center gap-1">
									<span class="inline-rating-score">
										<?php echo number_format($average_rating, 1); ?>
									</span>
									<i class="fa-solid fa-star inline-rating-star"></i>
									<?php if ($review_count > 0): ?>
										<span class="inline-rating-count">(
											<?php echo $review_count; ?>)
										</span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
						<p class="tax-info">(Tax Incl)</p>
					</div>

					<div class="d-flex justify-content-between align-items-start mb-2">
						<h1 class="product_title">
							<?php the_title(); ?>
						</h1>
					</div>
					<!-- Price Box -->
					<?php
					$coupon_posts = get_posts(array(
						'posts_per_page' => 10,
						'orderby' => 'name',
						'order' => 'asc',
						'post_type' => 'shop_coupon',
						'post_status' => 'publish',
					));

					?>
					<div class="trust-badge">
						<div class="svg-icon-conatiner">
							<?php $bis_logo_id = 2245;

							echo wp_get_attachment_image(
								$bis_logo_id,
								'small-icon',
								false,
								array(
									'alt'      => 'BIS hallmark',
									'class'    => 'svg-icon',
									'loading'  => 'lazy',
									'decoding' => 'async',
								)
							); ?>
							<span>BIS Hallmarked</span>
						</div>
						<div class="svg-icon-conatiner">
							<?php $silver_bar_id = 2247;

							echo wp_get_attachment_image(
								$silver_bar_id,
								'small-icon',
								false,
								array(
									'alt'      => 'Silver-bar',
									'class'    => 'svg-icon',
									'loading'  => 'lazy',
									'decoding' => 'async',
								)
							); ?>
							<span>92.5 Silver</span>
						</div>
						<div class="svg-icon-conatiner">
							<?php $hyoallergic_id = 2246;

							echo wp_get_attachment_image(
								$hyoallergic_id,
								'small-icon',
								false,
								array(
									'alt'      => 'hypoallergic',
									'class'    => 'svg-icon',
									'loading'  => 'lazy',
									'decoding' => 'async',
								)
							); ?>
							<span>Skin Friendly</span>
						</div>

					</div>
					<div class="woocommerce-variation-wrap">
						<?php woocommerce_template_single_add_to_cart(); ?>

					</div>
					<!-- <style>
						#btn-1cc-pdp {
							display: inline-flex;
							align-items: center;
							justify-content: center;
							gap: 8px;
							/* Creates clean spacing between the icon and text */
						}

						/* Forces the SVG icon to scale naturally with your button text height */
						.btn-custom-svg {
							height: 20px;
							/* Adjust this to match your design */
							width: auto;
							display: block;
							object-fit: contain;
						}
					</style> -->
					<!-- <script>
						document.addEventListener("DOMContentLoaded", function() {
							const pdpButton = document.getElementById("btn-1cc-pdp");

							if (pdpButton) {
								const originalText = pdpButton.textContent.trim();
								pdpButton.innerHTML = `<span class="btn-text">${originalText}</span>`;
								const iconImg = document.createElement("img");
								iconImg.src = ""; // <-- Put your SVG file path here
								iconImg.alt = "Payment Method";
								iconImg.className = "btn-custom-svg";
								pdpButton.append(iconImg);
							}
						});
					</script> -->
					<!-- COD Exchange Section -->
					<div class="cod-exchange-row">
						<div class="cod-exchange-item">
							<span class="cod-exchange-icon"><i class="fa-solid fa-rotate-left"></i></span>
							<span class="cod-exchange-text">Easy 3-day exchange &amp; return</span>
						</div>
						<div class="cod-exchange-item">
							<span class="cod-exchange-icon"><i class="fa-solid fa-money-bill-wave"></i></span>
							<span class="cod-exchange-text">Cash on Delivery available</span>
						</div>
						<div class="cod-exchange-item">
							<span class="cod-exchange-icon"><i class="fa-solid fa-shield-halved"></i></span>
							<span class="cod-exchange-text">Secure payments</span>
							<div class="cod-payment-logos">
								<!-- VISA -->
								<span class="payment-chip payment-chip--visa">
									<svg viewBox="0 0 48 18" width="34" height="13" xmlns="http://www.w3.org/2000/svg">
										<rect width="48" height="18" rx="3" fill="#1a1f71" />
										<text x="5" y="13" font-size="10" font-weight="800" fill="#fff" font-family="Arial,sans-serif" letter-spacing="1">VISA</text>
									</svg>
								</span>
								<!-- Mastercard -->
								<span class="payment-chip payment-chip--mc">
									<svg viewBox="0 0 40 26" width="20" height="13" xmlns="http://www.w3.org/2000/svg">
										<circle cx="15" cy="13" r="11" fill="#eb001b" />
										<circle cx="25" cy="13" r="11" fill="#f79e1b" />
										<path d="M20 5.5 A11 11 0 0 1 20 20.5 A11 11 0 0 1 20 5.5Z" fill="#ff5f00" />
									</svg>
								</span>
								<!-- UPI -->
								<span class="payment-chip payment-chip--upi">
									<svg viewBox="0 0 44 18" width="32" height="13" xmlns="http://www.w3.org/2000/svg">
										<rect width="44" height="18" rx="3" fill="#2d75e0" />
										<text x="5" y="13" font-size="9" font-weight="800" fill="#fff" font-family="Arial,sans-serif" letter-spacing="0.5">UPI</text>
									</svg>
								</span>
								<span class="payment-more">&amp; more</span>
							</div>
						</div>
					</div>

					<div class="stock-urgency-row"><span class="stock-dot"></span> Only 4 left in stock</div>
					<div class="delivery-estimate-row"><span class="delivery-dot"></span> Dispatched in 24–48 hrs ·
						Enter pincode above for exact delivery date</div>
					<div class="support-row">
						<i class="fa-regular fa-message"></i>
						Need help? <a href="https://api.whatsapp.com/send?phone=918010881801&text=Hi">Chat on WhatsApp</a>
						or call <a href="tel:+918010881801">+91 80108 81801</a>
					</div>
				</div>


			</div>

			<div class="sections-grid row mt-5 px-lg-4">
				<div class="col-lg-8">
					<?php
					$product_content = $product->get_description();
					if (!empty($product_content)): ?>
						<h3 class="fw-bold mb-4 fs-4">Product Description</h3>
						<div class="product-content text-muted mb-5">
							<?php echo apply_filters('the_content', $product_content); ?>
						</div>
					<?php endif; ?>

					<h3 class="fw-bold mb-4 fs-4">Detailed Specifications</h3>
					<table class="specifications-table">
						<tbody>
							<?php
							$custom_specs = get_post_meta($product->get_id(), '_product_specifications', true);
							$sku = $product->get_sku();


							// Build the full list with SKU auto-prepended
							$all_specs = array();

							if (!empty($sku)) {
								$all_specs[] = array('label' => 'SKU', 'value' => $sku);
							}

							if (!empty($custom_specs) && is_array($custom_specs)) {
								$all_specs = array_merge($all_specs, $custom_specs);
							}

							$attributes = $product->get_attributes();

							if (!empty($all_specs) && is_array($all_specs)):
								foreach ($all_specs as $spec): ?>
									<tr>
										<td class="spec-label">
											<?php echo esc_html($spec['label']); ?>
										</td>
										<td class="spec-value">
											<?php echo esc_html($spec['value']); ?>
										</td>
									</tr>
								<?php endforeach;
							elseif ($attributes):
								foreach ($attributes as $attribute):
									$name = wc_attribute_label($attribute->get_name());
									$value = array_values(wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'names')))[0];
								?>
									<tr>
										<td class="spec-label">
											<?php echo $name; ?>
										</td>
										<td class="spec-value">
											<?php echo $value; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>

					<div class="care-instructions mt-4">
						<h4 class="fw-bold mb-3">Care Instructions</h4>
						<ul class="text-muted small ps-3">
							<li>Store in a flat box to avoid scratches.</li>
							<li>Keep away from perfumes, sprays, and water.</li>
							<li>Wipe gently with a soft cloth after every use.</li>
						</ul>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="why-buy-widget mt-lg-0 mt-5">
						<h4 class="fw-bold mb-4 d-flex align-items-center gap-2">
							Why Buy From Us?
						</h4>
						<div class="why-buy-item">
							<div class="why-icon-wrap"><img
									src="<?php echo get_stylesheet_directory_uri() . '/assests/images/business.png'; ?>">
							</div>
							<div>
								<div class="why-title">Handcrafted in India</div>
								<div class="why-desc">Our jewelry is 100% handcrafted in India to help local artistians.
								</div>
							</div>
						</div>
						<div class="why-buy-item">
							<div class="why-icon-wrap"><img
									src="<?php echo get_stylesheet_directory_uri() . '/assests/images/skin-friendly.png'; ?>"></i>
							</div>
							<div>
								<div class="why-title">Skin Friendly</div>
								<div class="why-desc">Our jewelry is Nickel free and doesn't have any skin allergies
								</div>
							</div>
						</div>
						<div class="why-buy-item">
							<div class="why-icon-wrap"><img
									src="<?php echo get_stylesheet_directory_uri() . '/assests/images/silver-bars.png'; ?>">
							</div>
							<div>
								<div class="why-title">Precious metal</div>
								<div class="why-desc">Our jewelry is made using 92.5 sterling silver and other precious
									alloys</div>
							</div>
						</div>
					</div>
				</div>

			</div>

			<!-- 4. Reviews Section - Static Reviews Grid -->
			<div id="reviews" class="product-reviews-section mt-5 px-lg-4">
				<h3 class="fw-bold fs-4 text-center mb-5">Customer Reviews</h3>

				<div class="row g-4" id="reviewsGrid"></div>

				<div class="text-center mt-5">
					<button id="loadMoreBtn" class="d-none">Load More Reviews</button>
				</div>
			</div>

			<?php
			$comments = get_comments(array(
				'post_id' => $product_id,
				'status'  => 'approve',
				'type'    => 'review',
			));

			$reviews_data = array();
			foreach ($comments as $comment) {
				$rating = get_comment_meta($comment->comment_ID, 'rating', true);
				$reviews_data[] = array(
					'name'   => $comment->comment_author,
					'rating' => $rating ? intval($rating) : 5,
					'text'   => $comment->comment_content,
				);
			}
			?>
			<script>
				document.addEventListener("DOMContentLoaded", function() {
					const reviews = <?php echo json_encode($reviews_data); ?>;

					const ITEMS_PER_LOAD = 8; // 2 rows x 4 columns on desktop
					let itemsShown = 0;

					const grid = document.getElementById("reviewsGrid");
					const loadMoreBtn = document.getElementById("loadMoreBtn");

					if (!grid || !loadMoreBtn) return;

					function starsHtml(rating) {
						let html = "";
						for (let i = 0; i < 5; i++) {
							html += i < rating ? '<i class="fa-solid fa-star"></i> ' : '<i class="fa-regular fa-star"></i> ';
						}
						return html;
					}

					function renderReviews() {
						const nextBatch = reviews.slice(itemsShown, itemsShown + ITEMS_PER_LOAD);

						nextBatch.forEach(r => {
							const col = document.createElement("div");
							// 2 columns on mobile (col-6), 4 columns on desktop (col-lg-3)
							col.className = "col-6 col-lg-3 review-col";
							const initial = r.name.trim().charAt(0).toUpperCase();
							col.innerHTML = `
							<div class="review-card animate-fade-in-up">
								<div class="review-body">
									<div class="review-header">
										<div class="review-avatar">${initial}</div>
										<div>
											<div class="review-name">${r.name} <i class="fa-solid fa-check-circle verified-badge"></i></div>
											<div class="review-stars">${starsHtml(r.rating)}</div>
										</div>
									</div>
									<div class="review-text">${r.text}</div>
								</div>
							</div>
						`;
							grid.appendChild(col);
							// trigger visibility (allows for simple fade-in/slide-up)
							requestAnimationFrame(() => col.classList.add("visible"));
						});

						itemsShown += nextBatch.length;

						// toggle load more button
						if (itemsShown >= reviews.length) {
							loadMoreBtn.classList.add("d-none");
						} else {
							loadMoreBtn.classList.remove("d-none");
						}
					}

					loadMoreBtn.addEventListener("click", renderReviews);

					// initial load
					renderReviews();
				});
			</script>

			<?php
			//do_action('woocommerce_after_single_product_summary');
			?>

		</div>
	</div>

	<?php do_action('woocommerce_after_single_product'); ?>