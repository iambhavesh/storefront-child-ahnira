<?php

/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined('ABSPATH') || exit;

get_header('shop');

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action('woocommerce_before_main_content');

?>
<?php
/**
 * Dynamic Shop Banner Logic
 */
$banner_image_id = 0;
if (is_product_category()) {
	$queried_object = get_queried_object();
	$banner_image_id = get_term_meta($queried_object->term_id, 'thumbnail_id', true);
} elseif (is_shop() || is_product_tag()) {
	$banner_image_id = get_post_thumbnail_id(wc_get_page_id('shop'));
}

// Get the responsive URLs
$img_mobile  = wp_get_attachment_image_url($banner_image_id, 'medium_large');
$img_tablet  = wp_get_attachment_image_url($banner_image_id, 'large');
$img_desktop = wp_get_attachment_image_url($banner_image_id, 'full');

// Fallback if empty
if (!$img_desktop) {
	$img_mobile = $img_tablet = $img_desktop = get_stylesheet_directory_uri() . '/uploads/2026/07/shop_banner-scaled_compress-1024x572.webp';
}
?>


<header class="woocommerce-products-header shop-banner-header"
	style="--bg-mobile: url('<?php echo esc_url($img_mobile); ?>');
  --bg-tablet: url('<?php echo esc_url($img_tablet); ?>');
  --bg-desktop: url('<?php echo esc_url($img_desktop); ?>');">
	<div class="banner-overlay"></div>
	<div class="container-wide">
		<div class="banner-content text-center">
			<div id="page-breadcrumb" class="page-breadcrumb">
				<?php woocommerce_breadcrumb(); ?>
			</div>
		</div>
	</div>
</header>


<div class="container-fluid shop-main-page">
	<?php if (!is_product_category()) {

		// echo do_shortcode("[php_categories]");
	} ?>

	<div class="row tm-sticky-parent" data-sticky-group="left-sidebar">
		<div class="col-xl-3 sidebar-wrapper">
			<div class="sidebar sidebar-left tm-sticky-column sticky" data-sticky-group="left-sidebar">
				<div class="sidebar-inner ">
					<a href="#" class="btn-close-off-sidebar">
						<i class="fa-sharp fa-solid fa-xmark" style="color: #000;"></i>
					</a>
					<div class="sidebar-content">
						<div class="filter-header border-bottom mb-4">
							<div class="d-flex justify-content-between align-items-center mb-3">
								<h5 class="m-0">Filters</h5>
								<a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>"
									class="clear-all-filters">Clear All</a>
							</div>
						</div>
						<div class="sidebar-filters">
							<?php
							// Helper function to check if a filter is active
							function is_filter_active($taxonomy, $term_slug)
							{
								$filter_name = 'filter_' . str_replace('pa_', '', $taxonomy);
								if ($taxonomy === 'product_cat') {
									$get_cat = isset($_GET['product_cat']) ? explode(',', $_GET['product_cat']) : array();
									return is_product_category($term_slug) || in_array($term_slug, $get_cat);
								}
								$active_filters = isset($_GET[$filter_name]) ? explode(',', $_GET[$filter_name]) : array();
								return in_array($term_slug, $active_filters);
							}

							// 1. Price Range Filter
							global $wpdb;
							$prices = $wpdb->get_row("SELECT min(min_price) as min_price, max(max_price) as max_price FROM {$wpdb->prefix}wc_product_meta_lookup");
							$min_price = floor($prices->min_price);
							$max_price = ceil($prices->max_price);

							// Fallback if lookup table is empty or query fails
							if (empty($max_price)) {
								$min_price = 0;
								$max_price = 10000;
							}
							$current_min = isset($_GET['min_price']) ? $_GET['min_price'] : $min_price;
							$current_max = isset($_GET['max_price']) ? $_GET['max_price'] : $max_price;
							?>
							<div class="custom-filter-widget price-filter mb-5">
								<div class="filter-head d-flex justify-content-between align-items-center mb-3"
									data-bs-toggle="collapse" data-bs-target="#price-filter-body">
									<h3 class="m-0">Price Range <i class="fa fa-chevron-up"></i></h3>
								</div>
								<div id="price-filter-body" class="filter-body collapse show">
									<div class="price-slider-wrapper px-2">
										<input type="text" class="js-range-slider" name="price_range" value=""
											data-type="double" data-min="<?php echo trim($min_price); ?>"
											data-max="<?php echo trim($max_price); ?>"
											data-from="<?php echo trim($current_min); ?>"
											data-to="<?php echo trim($current_max); ?>" data-prefix="₹" />
									</div>
								</div>
							</div>

							<?php
							// 2. Attribute Filters
							$taxonomies = array(
								'product_cat' => 'Category',
								'pa_metal' => 'Metal',
								'product_tag' => 'Tag',
								'pa_size' => 'Size'
							);

							foreach ($taxonomies as $taxonomy => $label):
								$terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => true));
								if (!empty($terms)):
									$target_id = str_replace('pa_', '', $taxonomy) . '-filter-body';
							?>
									<div class="custom-filter-widget attribute-filter mb-4">
										<div class="filter-head d-flex justify-content-between align-items-center mb-3"
											data-bs-toggle="collapse" data-bs-target="#<?php echo $target_id; ?>">
											<h3 class="m-0"><?php echo $label; ?> <i class="fa fa-chevron-up"></i></h3>
										</div>
										<div id="<?php echo $target_id; ?>" class="filter-body collapse show">
											<ul class="list-unstyled custom-checkbox-list">
												<?php foreach ($terms as $term):
													$checked = is_filter_active($taxonomy, $term->slug) ? 'checked' : '';
												?>
													<li class="mb-2">
														<div class="custom-check">
															<input type="checkbox" id="filter-<?php echo $term->term_id; ?>"
																class="filter-checkbox" data-taxonomy="<?php echo $taxonomy; ?>"
																data-slug="<?php echo $term->slug; ?>" <?php echo $checked; ?>>
															<label
																for="filter-<?php echo $term->term_id; ?>"><?php echo $term->name; ?></label>
														</div>
													</li>
												<?php endforeach; ?>
											</ul>
										</div>
									</div>
							<?php
								endif;
							endforeach;
							?>
							<!-- <div class="custom-filter-widget attribute-filter mb-4">
								<?php //echo do_shortcode('[br_filter_single filter_id=1355]'); 
								?>
							</div> -->
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-9 col-lg-12 col-12">
			<?php
			// Build sorting options
			$catalog_orderby_options = apply_filters('woocommerce_catalog_orderby', array(
				'menu_order' => __('Default sorting', 'woocommerce'),
				'popularity' => __('Popularity', 'woocommerce'),
				'rating' => __('Average rating', 'woocommerce'),
				'date' => __('Latest', 'woocommerce'),
				'price' => __('Low to high', 'woocommerce'),
				'price-desc' => __('High to low', 'woocommerce'),
			));
			$current_orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby', 'menu_order'));
			$current_sort_label = isset($catalog_orderby_options[$current_orderby]) ? $catalog_orderby_options[$current_orderby] : __('Default sorting', 'woocommerce');
			?>
			<div class="row filter-row align-items-center mb-4">
				<!-- Mobile Filter Toggle & Orderby -->
				<div class="col-6 d-xl-none">
					<div class="filter-button-wrapper m-0">
						<i class="fa-solid fa-filter" style="color: #000000;"></i>
						<a href="#" class="btn-open-sidebar">
							<span class="button-text">Filters</span>
						</a>
					</div>
				</div>
				<div class="col-6 d-xl-none justify-content-end d-flex">
					<div class="custom-sort-wrapper">
						<button type="button" class="custom-sort-btn" id="custom-sort-btn-mobile" aria-expanded="false">
							<i class="fa-solid fa-arrow-up-wide-short"></i>
							<span class="sort-label"><?php echo esc_html($current_sort_label); ?></span>
							<i class="fa-solid fa-chevron-down sort-arrow"></i>
						</button>
						<ul class="custom-sort-dropdown" role="listbox">
							<?php foreach ($catalog_orderby_options as $key => $label): ?>
								<li class="sort-option <?php echo ($current_orderby === $key) ? 'active' : ''; ?>"
									data-value="<?php echo esc_attr($key); ?>">
									<?php echo esc_html($label); ?>
									<?php if ($current_orderby === $key): ?><i class="fa-solid fa-check"></i><?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>

				<!-- Result Count -->
				<div class="col-12 col-xl-auto mt-3 mt-xl-0">
					<div class="result-count-wrap">
						<?php echo woocommerce_result_count(); ?>
					</div>
				</div>

				<!-- Active Filters (New row on mobile, inline on desktop) -->
				<div class="col-12 col-xl-auto mt-2 mt-xl-0">
					<div class="active-filters-display d-flex flex-wrap gap-2">
						<?php
						// Display current category
						if (is_product_category()) {
							$current_cat = get_queried_object();
							echo '<span class="active-filter-chip" data-taxonomy="product_cat" data-slug="' . $current_cat->slug . '">' . $current_cat->name . ' <i class="fa fa-times"></i></span>';
						}

						// Display attribute filters
						foreach ($taxonomies as $taxonomy => $label) {
							$filter_name = 'filter_' . str_replace('pa_', '', $taxonomy);
							if (isset($_GET[$filter_name])) {
								$slugs = explode(',', $_GET[$filter_name]);
								foreach ($slugs as $slug) {
									$term = get_term_by('slug', $slug, $taxonomy);
									if ($term) {
										echo '<span class="active-filter-chip" data-taxonomy="' . $taxonomy . '" data-slug="' . $slug . '">' . $term->name . ' <i class="fa fa-times"></i></span>';
									}
								}
							}
							// Handle product_cat from URL as well
							if ($taxonomy === 'product_cat' && isset($_GET['product_cat']) && !is_product_category()) {
								$slugs = explode(',', $_GET['product_cat']);
								foreach ($slugs as $slug) {
									$term = get_term_by('slug', $slug, 'product_cat');
									if ($term) {
										echo '<span class="active-filter-chip" data-taxonomy="product_cat" data-slug="' . $slug . '">' . $term->name . ' <i class="fa fa-times"></i></span>';
									}
								}
							}
						}

						// Display price filter if active and different from min/max
						if (isset($_GET['min_price']) || isset($_GET['max_price'])) {
							echo '<span class="active-filter-chip price-chip" data-type="price">Price: ₹' . $current_min . ' - ₹' . $current_max . ' <i class="fa fa-times"></i></span>';
						}
						?>
					</div>
				</div>

				<!-- Desktop Orderby -->
				<div class="col-xl-auto d-none d-xl-block ms-auto">
					<div class="custom-sort-wrapper">
						<button type="button" class="custom-sort-btn" id="custom-sort-btn-desktop"
							aria-expanded="false">
							<i class="fa-solid fa-arrow-up-wide-short"></i>
							<span class="sort-label">
								<?php echo esc_html($current_sort_label); ?>
							</span>
							<i class="fa-solid fa-chevron-down sort-arrow"></i>
						</button>
						<ul class="custom-sort-dropdown" role="listbox">
							<?php foreach ($catalog_orderby_options as $key => $label): ?>
								<li class="sort-option <?php echo ($current_orderby === $key) ? 'active' : ''; ?>"
									data-value="<?php echo esc_attr($key); ?>">
									<?php echo esc_html($label); ?>
									<?php if ($current_orderby === $key): ?><i class="fa-solid fa-check"></i>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
			<style>
				/* ── Custom Sort Dropdown ── */
				.custom-sort-wrapper {
					position: relative;
					display: inline-flex;
					align-items: center;
				}

				.custom-sort-btn {
					display: inline-flex;
					align-items: center;
					gap: 6px;
					background: #f8f8f8;
					border: none;
					padding: 10px;
					cursor: pointer;
					font-size: 14px;
					font-weight: 500;
					color: #000;
					line-height: 1;
					white-space: nowrap;
					outline: none;
					transition: opacity 0.2s;
					border-radius: 50px;
				}

				.custom-sort-btn:hover {
					opacity: 0.7;
				}

				.custom-sort-btn .sort-arrow {
					font-size: 11px;
					transition: transform 0.25s ease;
				}

				.custom-sort-btn.open .sort-arrow {
					transform: rotate(180deg);
				}

				.custom-sort-dropdown {
					display: none;
					position: absolute;
					top: calc(100% + 10px);
					right: 0;
					min-width: 220px;
					background: #fff;
					border: 1px solid #e0e0e0;
					border-radius: 6px;
					box-shadow: 0 6px 24px rgba(0, 0, 0, 0.10);
					padding: 6px 0;
					margin: 0;
					list-style: none;
					z-index: 9999;
					animation: sortFadeIn 0.18s ease;
				}

				@keyframes sortFadeIn {
					from {
						opacity: 0;
						transform: translateY(-6px);
					}

					to {
						opacity: 1;
						transform: translateY(0);
					}
				}

				.custom-sort-dropdown.open {
					display: block;
				}

				.sort-option {
					display: flex;
					align-items: center;
					justify-content: space-between;
					padding: 10px 18px;
					font-size: 13px;
					color: #333;
					cursor: pointer;
					transition: background 0.15s;
					gap: 10px;
				}

				.sort-option:hover {
					background: #f5f5f5;
				}

				.sort-option.active {
					font-weight: 600;
					color: #000;
				}

				.sort-option .fa-check {
					font-size: 12px;
					color: #000;
				}
			</style>

			<script>
				(function() {
					function initSortDropdown(btn) {
						if (!btn) return;
						var dropdown = btn.nextElementSibling;
						btn.addEventListener('click', function(e) {
							e.stopPropagation();
							var isOpen = dropdown.classList.toggle('open');
							btn.classList.toggle('open', isOpen);
							btn.setAttribute('aria-expanded', isOpen);
						});
						dropdown.querySelectorAll('.sort-option').forEach(function(option) {
							option.addEventListener('click', function() {
								var val = this.getAttribute('data-value');
								var url = new URL(window.location.href);
								url.searchParams.set('orderby', val);
								window.location.href = url.toString();
							});
						});
					}
					document.addEventListener('DOMContentLoaded', function() {
						initSortDropdown(document.getElementById('custom-sort-btn-mobile'));
						initSortDropdown(document.getElementById('custom-sort-btn-desktop'));
						// Close on outside click
						document.addEventListener('click', function() {
							document.querySelectorAll('.custom-sort-dropdown.open').forEach(function(d) {
								d.classList.remove('open');
								d.previousElementSibling.classList.remove('open');
								d.previousElementSibling.setAttribute('aria-expanded', 'false');
							});
						});
					});
				})();
			</script>
			<div class="shop-page">
				<?php
				if (woocommerce_product_loop()) {

					woocommerce_product_loop_start();

					if (wc_get_loop_prop('total')) {
						while (have_posts()) {
							the_post();

							/**
							 * Hook: woocommerce_shop_loop.
							 */
							do_action('woocommerce_shop_loop');

							wc_get_template_part('content', 'product');
						}
					}

					woocommerce_product_loop_end();
				} else {
				?>
					<div class="no-products-found-container text-center py-5">
						<div class="no-roduct-info mb-4">
							<?php esc_html_e('No products were found matching your selection.', 'woocommerce'); ?>
						</div>
						<div class="search-other-products">
							<h3 class="mb-3">
								<?php esc_html_e('Continue Shopping for other Products', 'storefront-child-ahnira'); ?>
							</h3>
						</div>
					</div>
				<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
<?php
if (woocommerce_product_loop()) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	// do_action('woocommerce_before_shop_loop');



	// woocommerce_product_loop_start();

	// if (wc_get_loop_prop('total')) {
	// 	while (have_posts()) {
	// 		the_post();

	// 		/**
	// 		 * Hook: woocommerce_shop_loop.
	// 		 */
	// 		do_action('woocommerce_shop_loop');

	// 		wc_get_template_part('content', 'product');
	// 	}
	// }

	// woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	//do_action('woocommerce_after_shop_loop');
	echo woocommerce_pagination();
?>
<?php
}

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('woocommerce_after_main_content');

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
// do_action('woocommerce_sidebar');

get_footer('shop');
