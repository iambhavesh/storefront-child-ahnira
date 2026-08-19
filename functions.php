<?php

/**
 * Load Custom Comments Layout file.
 */

require_once(get_theme_file_path() . '/inc/comments-helper.php');
add_action('after_setup_theme', function () {
	add_theme_support('woocommerce');
});

function child_enqueue_styles()
{
	wp_enqueue_style('bootstrap5-css', get_stylesheet_directory_uri() . '/assests/css/bootstrap/bootstrap.min.css');
	//wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');
	wp_enqueue_style('fontawesome-all', get_stylesheet_directory_uri() . '/assests/fontawesome/css/all.min.css');
	wp_enqueue_style('fancybox-cdn-css', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css', array(), '5.0');
	wp_enqueue_style('carousel-image-slider-swiper', get_stylesheet_directory_uri() . '/assests/css/swiper/swiper.bundle.min.css');
	if (is_product_category() || is_shop() || is_product_tag()) {
		wp_enqueue_style('ion-range-slider', get_stylesheet_directory_uri() . '/assests/css/ion.rangeSlider.min.css');
	}

	// Enqueue the necessary JavaScript
	wp_enqueue_script('bootstrap-js', get_stylesheet_directory_uri() . '/assests/js/bootstrap/bootstrap.bundle.min.js', array('jquery'), '', true);
	wp_enqueue_script('fancybox-cdn-js', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', array('jquery'), '5.0', true);
	wp_enqueue_script('carousel-image-slider-swiper', get_stylesheet_directory_uri() . '/assests/js/swiper/swiper-bundle.min.js', array('jquery'), '1.0.0', true);
	wp_enqueue_script('script', get_stylesheet_directory_uri() . '/assests/js/script.min.js', array('jquery'), '1.0.0', true);
	if (is_product()) {
		wp_enqueue_script('hc-sticky-js', get_stylesheet_directory_uri() . '/assests/js/hc-sticky/hc-sticky.min.js', array('jquery'), '', true);
	}
	wp_enqueue_script('footer-js', get_stylesheet_directory_uri() . '/assests/js/footer.min.js', array('jquery'), '', true);
	wp_enqueue_script('swatch-variation', get_stylesheet_directory_uri() . '/assests/js/swatch-variation.min.js', array('jquery'), '', true);

	if (is_product_category() || is_shop() || is_product_tag()) {
		wp_enqueue_script('ion-range-slider',  get_stylesheet_directory_uri() . '/assests/js/ion.rangeSlider.min.js', array('jquery'), '2.3.1', true);
		wp_enqueue_script('ion-range-slider-2',  get_stylesheet_directory_uri() . '/assests/js/ionrangeslider.js', array('jquery'), '2.3.1', true);
	}
	if (is_page('cart') || is_cart()) {
		wp_enqueue_script('cart-js', get_stylesheet_directory_uri() . '/assests/js/cart.js', array('jquery'), time(), true);
		wp_localize_script('cart-js', 'ahnira_cart_params', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ahnira-cart-nonce'),
		));
	}
	// Localize the ajaxurl variable
	wp_localize_script('footer-js', 'custom_ajax_obj', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
	));
}
add_action('wp_enqueue_scripts', 'child_enqueue_styles', 10);

add_filter('woocommerce_add_to_cart_fragments', 'ahnira_cart_count_fragment');
function ahnira_cart_count_fragment($fragments)
{
	$count = WC()->cart->get_cart_contents_count();
	$fragments['#mini-cart-count'] = '<div id="mini-cart-count" class="cart-count badge">' . $count . '</div>';
	return $fragments;
}
add_action('wp_enqueue_scripts', 'ahnira_disable_live_sales_notification_on_conversion_pages', 99);
function ahnira_disable_live_sales_notification_on_conversion_pages()
{
	// is_checkout() covers checkout form + order-received/thank-you + order confirmation.
	if (is_cart() || is_checkout()) {
		wp_dequeue_script('pisol-sales-notification-popup');
		wp_dequeue_script('pisol-sales-notification-runner');
		wp_deregister_script('pisol-sales-notification-popup');
		wp_deregister_script('pisol-sales-notification-runner');
	}
}
/**
 * AJAX Apply Coupon
 */
add_action('wp_ajax_ahnira_apply_coupon', 'ahnira_apply_coupon_handler');
add_action('wp_ajax_nopriv_ahnira_apply_coupon', 'ahnira_apply_coupon_handler');
function ahnira_apply_coupon_handler()
{
	check_ajax_referer('ahnira-cart-nonce', 'nonce');

	if (!WC()->session->has_session()) {
		WC()->session->set_customer_session_cookie(true);
	}

	$coupon_code = sanitize_text_field($_POST['coupon_code']);

	if (!empty($coupon_code)) {
		WC()->cart->add_discount($coupon_code);
	}

	WC()->cart->calculate_totals();

	$notices = wc_print_notices(true);
	$fragments = apply_filters('woocommerce_add_to_cart_fragments', array());
	$fragments['.woocommerce-notices-wrapper'] = '<div class="woocommerce-notices-wrapper">' . $notices . '</div>';

	wp_send_json(array(
		'success' => true,
		'fragments' => $fragments,
		'notices' => $notices,
		'data' => array('notices' => $notices),
		'cart_hash' => WC()->cart->get_cart_hash(),
	));
	die();
}

/**
 * AJAX Remove Coupon
 */
add_action('wp_ajax_ahnira_remove_coupon', 'ahnira_remove_coupon_handler');
add_action('wp_ajax_nopriv_ahnira_remove_coupon', 'ahnira_remove_coupon_handler');
function ahnira_remove_coupon_handler()
{
	check_ajax_referer('ahnira-cart-nonce', 'nonce');

	$coupon_code = sanitize_text_field($_POST['coupon_code']);

	if (!empty($coupon_code)) {
		WC()->cart->remove_coupon($coupon_code);
	}

	WC()->cart->calculate_totals();

	$notices = wc_print_notices(true);
	$fragments = apply_filters('woocommerce_add_to_cart_fragments', array());
	$fragments['.woocommerce-notices-wrapper'] = $notices;

	wp_send_json(array(
		'success' => true,
		'fragments' => $fragments,
		'notices' => $notices,
		'data' => array('notices' => $notices),
		'cart_hash' => WC()->cart->get_cart_hash(),
	));
	die();
}

function themename_custom_logo_setup()
{
	$defaults = array(
		'height' => 100,
		'width' => 400,
		'flex-height' => true,
		'flex-width' => true,
		'header-text' => array('site-title', 'site-description'),
		'unlink-homepage-logo' => true,
		'fetchpriority' => 'high',
		'loading' => 'eager',
	);
	add_theme_support('custom-logo', $defaults);
}
add_action('after_setup_theme', 'themename_custom_logo_setup');
/**
 * Get Coupon Section HTML for AJAX fragments and Cart template
 */
function ahnira_cart_coupon_section_html()
{
	if (!wc_coupons_enabled()) {
		return '';
	}

	$applied_coupons = WC()->cart->get_applied_coupons();
	$coupons = get_posts(array(
		'posts_per_page' => -1,
		'post_type' => 'shop_coupon',
		'post_status' => 'publish',
	));
	ob_start();
?>
	<div class="ahnira-coupon-fragment-wrapper">
		<div class="ahnira-cart-coupon-section ahnira-coupon-section coupon-box bg-white p-4 border rounded mb-4">
			<!-- <div class="d-flex align-items-center mb-3">
				<i class="fa-solid fa-tag me-2"></i>
				<h6 class="mb-0 fw-bold">Apply Coupons</h6>
			</div> -->

			<?php if (empty($applied_coupons)): ?>
				<!-- <div class="ahnira-cart-coupon-input-wrapper d-flex gap-2">
					<input type="text" name="coupon_code" class="ahnira-cart-coupon-input form-control" id="coupon_code"
						value="" placeholder="Enter coupon code" />
					<button type="button" class="ahnira-cart-apply-coupon button btn-apply-coupon" name="apply_coupon"
						value="Apply">APPLY</button>
				</div> -->
			<?php else:
				$coupon_code = strtoupper($applied_coupons[0]);
			?>
				<!-- <div
					class="ahnira-cart-coupon-input-wrapper coupon-applied d-flex align-items-center justify-content-between p-2 border rounded bg-light">
					<span
						class="ahnira-cart-coupon-applied-text small"><?php //printf(__('Coupon: %s', 'sidekart'), '<strong>' . $coupon_code . '</strong>'); 
																		?></span>
					<button type="button" class="ahnira-cart-remove-coupon btn btn-sm btn-link text-danger p-0 ms-2"
						data-code="<?php //echo esc_attr($applied_coupons[0]); 
									?>"
						style="text-decoration: none; font-weight: 700;">REMOVE</button>
				</div> -->
			<?php endif; ?>

			<?php
			if (!empty($coupons)): ?>
				<div class="ahnira-cart-view-coupons-wrapper mt-3">
					<a href="#" class="ahnira-cart-view-coupons-link d-inline-block active small"
						style="text-decoration: none;">
						<i class="fa-solid fa-ticket me-1"></i> View Available Coupons <span class="toggle-arrow">▼</span>
					</a>
					<div class="ahnira-cart-coupons-list" style="overflow-y: auto;display:block;">
						<?php foreach ($coupons as $coupon_post):
							$coupon = new WC_Coupon($coupon_post->post_title);
							if (!$coupon->get_id())
								continue;

							$discount_type = $coupon->get_discount_type();
							$amount = $coupon->get_amount();
							$description = $coupon->get_description();

							$discount_display = '';
							if ($discount_type === 'percent') {
								$discount_display = $amount . '% off';
							} elseif ($discount_type === 'fixed_cart' || $discount_type === 'fixed_product') {
								$discount_display = wc_price($amount) . ' off';
							}
						?>
							<div class="ahnira-cart-coupon-item  py-2"
								data-code="<?php echo esc_attr($coupon_post->post_title); ?>">
								<div class="coupon-info">
									<span class="coupon-code fw-bold d-block"
										style="font-size: 13px;"><?php echo esc_html(strtoupper($coupon_post->post_title)); ?></span>
									<?php if ($discount_display): ?>
										<span class="coupon-discount text-success small fw-bold"><?php echo $discount_display; ?></span>
									<?php endif; ?>
								</div>
								<button type="button" class="ahnira-cart-coupon-copy-btn" style="font-size: 10px; padding: 2px 8px;"
									onclick="navigator.clipboard.writeText('<?php echo esc_js($coupon_post->post_title); ?>'); var btn = this; btn.innerText = 'COPIED!'; setTimeout(function() { btn.innerText = 'COPY'; }, 2000);">COPY</button>
								<!-- <button type="button" class="ahnira-cart-coupon-apply-btn btn btn-sm btn-outline-dark"
									style="font-size: 10px; padding: 2px 8px;">APPLY</button> -->

								<?php if ($description): ?>
									<!-- <div class="coupon-description text-muted xsmall mt-1" style="font-size: 11px;">
										<?php //echo esc_html($description); 
										?>
									</div> -->
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php
	return ob_get_clean();
}
add_filter('wc_get_template', function ($template, $template_name) {
	if ($template_name === 'cart/cart.php') {
		$custom = get_stylesheet_directory() . '/woocommerce/cart/cart.php';
		if (file_exists($custom))
			return $custom;
	}
	return $template;
}, 999, 2);

// functions.php
add_filter('woocommerce_locate_template', function ($template, $template_name, $template_path) {
	$child_template = get_stylesheet_directory() . '/woocommerce/' . $template_name;
	if (file_exists($child_template)) {
		return $child_template;
	}
	return $template;
}, 99, 3);



/**
 * Remove breadcrumbs for Storefront theme
 */
add_action('init', 'wc_remove_storefront_breadcrumbs');
function wc_remove_storefront_breadcrumbs()
{
	remove_action('storefront_before_content', 'woocommerce_breadcrumb', 10);
}

add_action('woocommerce_before_shop_loop', 'wc_remove_storefront_woocommerce_result_count', 1);
function wc_remove_storefront_woocommerce_result_count()
{
	remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
}

add_action('woocommerce_before_shop_loop', 'wc_remove_storefront_woocommerce_catalog_ordering', 1);
function wc_remove_storefront_woocommerce_catalog_ordering()
{
	remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10);
}

add_action('woocommerce_before_single_product_summary', 'wc_remove_woocommerce_show_product_images', 1);
function wc_remove_woocommerce_show_product_images()
{
	remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
}
add_action('woocommerce_before_single_product_summary', 'wc_remove_woocommerce_show_product_sale_flash', 1);
function wc_remove_woocommerce_show_product_sale_flash()
{
	remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
}

add_action('woocommerce_single_product_summary', 'wc_remove_storefront_woocommerce_single_product_summary', 1);
function wc_remove_storefront_woocommerce_single_product_summary()
{
	remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
}

add_action('woosq_product_summary', 'wc_remove_storefront_woosq_product_summary', 1);
function wc_remove_storefront_woosq_product_summary()
{
	remove_action('woosq_product_summary', 'woocommerce_template_single_rating', 10);
}
/**
 * Change number or products per row to 3
 */
add_filter('loop_shop_columns', 'loop_columns', 999);
if (!function_exists('loop_columns')) {
	function loop_columns()
	{
		return 4; // 4 products per row
	}
}

add_action('woocommerce_after_shop_loop_item', 'wc_remove_storefront_woocommerce_after_shop_loop_item', 1);
function wc_remove_storefront_woocommerce_after_shop_loop_item()
{
	remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
}
function shop_sidebar()
{

	register_sidebar(array(
		'name' => 'Shop Sidebar',
		'id' => 'shop-sidebar',
		'description' => 'Left Sidebar for the Shop Page',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	));

	register_sidebar(array(
		'name' => 'Footer About',
		'id' => 'footer-about',
		'description' => 'Footer About section – editable via Elementor or Widgets',
		'before_widget' => '<div class="footer-about-widget">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="footer-title">',
		'after_title' => '</h4>',
	));

	register_sidebar(array(
		'name' => 'Footer Newsletter',
		'id' => 'footer-newsletter',
		'description' => 'Footer Newsletter section – editable via Elementor or Widgets',
		'before_widget' => '<div class="footer-newsletter-widget">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="footer-title">',
		'after_title' => '</h4>',
	));
}
add_action("woocommerce_available_coupon", "woocommerce_output_available_coupons", 20);
function woocommerce_output_available_coupons()
{
	wc_get_template('single-product/coupon.php');
}

add_action("woocommerce_share", "woocommerce_output_coupons", 20);
function woocommerce_output_coupons()
{
	wc_get_template('single-product/coupon.php');
}

add_action('widgets_init', 'shop_sidebar');


// remove Order Notes from checkout field in Woocommerce
add_filter('woocommerce_checkout_fields', 'alter_woocommerce_checkout_fields');
function alter_woocommerce_checkout_fields($fields)
{
	unset($fields['order']['order_comments']);
	return $fields;
}
add_action('woocommerce_sale_flash', 'sale_badge_percentage', 25);

function sale_badge_percentage()
{
	global $product;
	if (!$product->is_on_sale())
		return;
	if ($product->is_type('simple')) {
		$max_percentage = (($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100;
	} elseif ($product->is_type('variable')) {
		$max_percentage = 0;
		foreach ($product->get_children() as $child_id) {
			$variation = wc_get_product($child_id);
			$price = $variation->get_regular_price();
			$sale = $variation->get_sale_price();
			if ($price != 0 && !empty($sale))
				$percentage = ($price - $sale) / $price * 100;
			if ($percentage > $max_percentage) {
				$max_percentage = $percentage;
			}
		}
	}
	if ($max_percentage > 0)
		echo "<span class='onsale'>-" . round($max_percentage) . "%</span>";
}

function wd_customize_comment_form_fields($fields)
{

	// get value of fields and save as variable
	$author = $fields['author'];
	$email = $fields['email'];
	$comment = $fields['comment'];
	$cookies_field = $fields['cookies'];

	$commenter = wp_get_current_commenter();
	$consent = empty($commenter['comment_author_email']) ? '' : ' checked="checked"';

	// unset other fields to allow reordering
	unset($fields['url']);
	unset($fields['comment']);
	unset($fields['author']);
	unset($fields['email']);
	unset($fields['cookies']);

	// set fields in proper order with placeholder
	$fields['author'] = $author;
	$fields['email'] = $email;
	$fields['comment'] = $comment;
	//$fields['cookies'] = $cookies_field;
	$fields['cookies'] = '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' />' . '<label for="wp-comment-cookies-consent">Save my name and email in this browser for the next time I comment.</label></p>';

	return $fields;
}
add_filter('comment_form_fields', 'wd_customize_comment_form_fields');

//function to call first uploaded image in functions file
function main_image()
{
	$files = get_children('post_parent=' . get_the_ID() . '&post_type=attachment
	&post_mime_type=image&order=desc');
	if ($files):
		$keys = array_reverse(array_keys($files));
		$j = 0;
		$num = $keys[$j];
		$image = wp_get_attachment_image($num, 'large', true);
		$imagepieces = explode('"', $image);
		$imagepath = $imagepieces[1];
		$main = wp_get_attachment_url($num);
		$template = get_template_directory();
		$the_title = get_the_title();
		if ($main):
			return "<img src='$main' width='740' height='480' data-src='$main' alt='$the_title' class='frame' />";
		else:
			return false;
		endif;
	endif;
}

// Add a custom field to the WooCommerce shipping settings with currency symbol
function custom_woocommerce_shipping_settings($settings)
{
	$currency_symbol = get_woocommerce_currency_symbol();

	$settings[] = array(
		'title' => __('Free Shipping Limit', 'woocommerce'),
		'id' => 'free_shipping_limit',
		'type' => 'number',
		'css' => 'min-width: 100px;',
		'default' => '0',  // Set the default value to 0
		'desc_tip' => true,
	);
	return $settings;
}
add_filter('woocommerce_shipping_settings', 'custom_woocommerce_shipping_settings');

// Display the custom field and currency symbol within the existing table form-table
function display_custom_shipping_fields()
{
	$currency_symbol = get_woocommerce_currency_symbol('INR');
?>
	<tr valign="top">
		<th scope="row" class="titledesc"><?php _e('Free Shipping Limit', 'woocommerce'); ?></th>
		<td class="forminp">
			<fieldset>
				<legend class="screen-reader-text"><span><?php _e('Free Shipping Limit', 'woocommerce'); ?></span></legend>
				<input type="number" min="0" step="1" name="free_shipping_limit" id="free_shipping_limit"
					value="<?php echo esc_attr(get_option('free_shipping_limit', 0)); ?>" class="input-text"
					style="min-width: 70px;" />
				<?php echo '<span class="currency-symbol">' . $currency_symbol . '</span>'; ?>
				<br />
				<span
					class="description"><?php _e('Enter the free shipping limit (up to 7 digits)', 'woocommerce'); ?></span>
			</fieldset>
		</td>
	</tr>
<?php
}
add_action('woocommerce_shipping_table_rate_after_rows', 'display_custom_shipping_fields');

// Save the custom field value
function save_custom_woocommerce_shipping_settings()
{
	$free_shipping_limit = absint($_POST['free_shipping_limit']);
	update_option('free_shipping_limit', $free_shipping_limit);
}
add_action('woocommerce_update_options_shipping', 'save_custom_woocommerce_shipping_settings');

/**
 * Display color or image attributes on single product page using shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output for color or image attributes.
 */
function display_attributes_shortcode($atts)
{
	$atts = shortcode_atts(array(
		'id' => 0,
	), $atts, 'display_attributes');


	$product_id = intval($atts['id']);

	if ($product_id <= 0) {
		return ''; // Invalid product ID.
	}

	$product = wc_get_product($product_id);

	if (!$product || !$product->is_type('variable')) {
		return ''; // Product not found or not variable.
	}
	$output = '';
	$variations = $product->get_available_variations();

	$output .= '<div class="swatches-wrapper">';
	$variation_attributes_processed = array();

	$attributes = $product->get_attributes();

	foreach ($attributes as $attribute) {

		$taxonomy = $attribute->get_taxonomy_object();
		$attribute_type = $taxonomy->attribute_type;

		foreach ($variations as $variation) {
			foreach ($variation['attributes'] as $name => $value) {
				if ($attribute_type === 'image' && !in_array($value, $variation_attributes_processed)) {
					$terms = $attribute->get_terms();

					foreach ($terms as $term) {
						print_r($term);
					}

					$taxonomy = str_replace('attribute_', '', $name);
					$term = get_term_by('slug', $value, $taxonomy);

					if ($term) {
						$attribute_image_id = get_term_meta($term->term_id, 'product_attribute_image', true);
						$attribute_image_url = wp_get_attachment_image_url($attribute_image_id);
						$color_label = $term->name;
						$image_url = $variation['image']['url'];

						$output .= '<a href="#" class="hint--top product-variations-swatch varation-id-' . $color_label . '" aria-label="' . $color_label . '" data-variation_image="' . esc_url($image_url) . '">';
						$output .= '<span class="swatch-circle" style=" border-radius: 50%;"><img src="' . $attribute_image_url . '"> </span>';
						$output .= '<span class="swatch-circle-border"></span>';
						$output .= '<span class="swatch-name">' . $color_label . '</span>';
						$output .= '</a>';
					}
					$variation_attributes_processed[] = $value;
				} elseif (($attribute_type === 'color' && strpos($name, 'color') !== false) && !in_array($value, $variation_attributes_processed)) {

					$taxonomy = str_replace('attribute_', '', $name);
					$term = get_term_by('slug', $value, $taxonomy);
					if ($term) {
						$color_value = get_term_meta($term->term_id, 'product_attribute_color', true);
						$color_label = $term->name;
						$image_url = $variation['image']['url'];

						$output .= '<a href="#" class="hint--top product-variations-swatch varation-id-' . $color_label . '" aria-label="' . $color_label . '" data-variation_image="' . esc_url($image_url) . '">';
						$output .= '<span class="swatch-circle" style="background-color: ' . esc_attr($color_value) . '; border-radius: 50%;"></span>';
						$output .= '<span class="swatch-circle-border"></span>';
						$output .= '<span class="swatch-name">' . $color_label . '</span>';
						$output .= '</a>';
					}
					$variation_attributes_processed[] = $value;
				}
			}
		}
	}

	$output .= '</div>';

	// Output the color attribute links
	$output .= '<div class="display-color-attributes-js"></div>';
	return $output;
}
add_shortcode('display_attributes', 'display_attributes_shortcode');

// Shortcode to output custom PHP in Elementor
function wpc_elementor_shortcode_categories($atts)
{ ?>

	<div class="container">
		<div class="category-wrapper categorgy-swiper swiper mb-2 row">
			<div class="category-slider swiper-wrapper">
				<?php
				$orderby = 'name';
				$order = 'asc';
				$hide_empty = false;
				$cat_args = array(
					'orderby' => $orderby,
					'order' => $order,
					'hide_empty' => $hide_empty,
					'parent' => 0
				);

				$product_categories = get_terms('product_cat', $cat_args);

				if (!empty($product_categories)) {
					//print_r($product_categories);
					foreach ($product_categories as $key => $category) {
						if ($category->slug === 'uncategorized')
							continue;

						$category_id = $category->term_id;
						$category_name = $category->name;
						$image_url = wp_get_attachment_url(get_term_meta($category->term_id, 'thumbnail_id', true)); // Get image Url
						$image = '<img src="' . $image_url . '" width="180" height="180" alt="cat-fashion-03" data-src="' . $image_url . '" class="">';
				?>
						<div class="swiper-slide">
							<a href="<?php echo get_term_link($category); ?>">
								<div class="category-slide-item">
									<div class="category-image">
										<div class="image">
											<?php echo empty($image_url) ? ' ' : $image; ?>
										</div>
									</div>
									<div class="category-info">
										<h3><?php echo $category_name; ?></h3>
									</div>
								</div>
							</a>
						</div>
				<?php }
				}
				?>
			</div>
		</div>
	</div>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
	<script>
		const CategorgySwiper = new Swiper(".categorgy-swiper", {
			slidesPerView: 2.5,
			spaceBetween: 20,
			breakpoints: {
				1201: {
					slidesPerView: 5,
					spaceBetween: 2,
				},
				// when window width is >= 769px
				769: {
					slidesPerView: 4.5,
					spaceBetween: 20,
				},
				577: {
					slidesPerView: 2.5,
					spaceBetween: 20,
				},
			},
		});
	</script>
<?php }
add_shortcode('php_categories', 'wpc_elementor_shortcode_categories');


add_action('woocommerce_before_calculate_totals', 'merge_duplicated_products_in_cart');
function merge_duplicated_products_in_cart($cart)
{
	if ((is_admin() && !defined('DOING_AJAX')))
		return;

	if (did_action('woocommerce_before_calculate_totals') >= 2)
		return;

	$items_data = $item_update = []; // initializing

	// Loop through cart items
	foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
		$product_id = $cart_item['data']->get_id();
		$quantity = $cart_item['quantity'];

		// Check if the product exists
		if (in_array($product_id, array_keys($items_data))) {
			// Same product found
			$item_update['item_qty'] = $items_data[$product_id]['qty'] + $quantity; // Set cumulated quantities
			$item_update['item_key'] = $items_data[$product_id]['key']; // Add first product item key
			$item_update['item_remove'] = $cart_item_key; // Add current item key (product to be removed)
			break; // Stop the loop
		}
		// Add product_id, cart item key and item quantity to the array (for each item)
		else {
			$items_data[$product_id] = array(
				'key' => $cart_item_key,
				'qty' => $quantity
			);
		}
	}
	unset($items_data); // delete the variable

	if (!empty($item_update)) {
		$cart->remove_cart_item($item_update['item_remove']);
		$cart->set_quantity($item_update['item_key'], $item_update['item_qty']);
		unset($item_update); // delete the variable
	}
}

add_action('woocommerce_before_calculate_totals', 'custom_price_cart', 10, 1);

function custom_price_cart($cart)
{
	if (is_admin() && !defined('DOING_AJAX'))
		return;

	foreach ($cart->get_cart() as $cart_item_key => $cart_item) {

		if (array_key_exists('custom_price', $cart_item)) {
			$custom_price = $cart_item['custom_price'];
			if ($custom_price) {
				$cart_item['data']->set_price($custom_price);
			}
		}
	}
}

/**
 * Add a custom tab to Product Data meta box.
 */
function add_specs_product_data_tab($tabs)
{
	$tabs['specs'] = array(
		'label' => __('Specifications', 'woocommerce'),
		'target' => 'specs_product_data',
		'class' => array('show_if_simple', 'show_if_variable'),
		'priority' => 25,
	);
	return $tabs;
}
add_filter('woocommerce_product_data_tabs', 'add_specs_product_data_tab');

/**
 * Add the content for the custom tab.
 */
function specs_product_data_panel()
{
	global $post;
	$specs = get_post_meta($post->ID, '_product_specifications', true);
	$sku = get_post_meta($post->ID, '_sku', true);
?>
	<div id="specs_product_data" class="panel woocommerce_options_panel hidden">
		<div class="options_group">
			<p class="form-field">
				<label><?php _e('Product Specifications', 'woocommerce'); ?></label>
			<div class="product-specs-rows-container" style="padding: 0 10px 10px;">
				<table class="widefat" id="product-specs-table">
					<thead>
						<tr>
							<th><?php _e('Label', 'woocommerce'); ?></th>
							<th><?php _e('Value', 'woocommerce'); ?></th>
							<th style="width: 50px;"></th>
						</tr>
					</thead>
					<tbody>
						<!-- Auto SKU row (read-only, always first) -->
						<tr class="sku-spec-row" style="background: #f9f9f9;">
							<td>
								<input type="text" value="<?php _e('SKU', 'woocommerce'); ?>"
									style="width:100%; font-weight:bold; background:#f0f0f0;" readonly />
							</td>
							<td>
								<input type="text" id="spec-sku-value"
									value="<?php echo esc_attr($sku ?: __('Not set', 'woocommerce')); ?>"
									style="width:100%; background:#f0f0f0;" readonly />
							</td>
							<td>
								<span style="color:#999; font-size:11px; display:block; text-align:center;">auto</span>
							</td>
						</tr>
						<?php
						if (!empty($specs) && is_array($specs)) {
							foreach ($specs as $index => $spec) {
						?>
								<tr>
									<td><input type="text" name="spec_label[]" value="<?php echo esc_attr($spec['label']); ?>"
											placeholder="e.g. Material" style="width:100%;" /></td>
									<td><input type="text" name="spec_value[]" value="<?php echo esc_attr($spec['value']); ?>"
											placeholder="e.g. 22KT Gold" style="width:100%;" /></td>
									<td><a href="#" class="button remove-spec-row">×</a></td>
								</tr>
							<?php
							}
						} else {
							?>
							<tr>
								<td><input type="text" name="spec_label[]" value="" placeholder="e.g. Material"
										style="width:100%;" /></td>
								<td><input type="text" name="spec_value[]" value="" placeholder="e.g. 22KT Gold"
										style="width:100%;" /></td>
								<td><a href="#" class="button remove-spec-row">×</a></td>
							</tr>
						<?php
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="3">
								<a href="#" class="button button-primary"
									id="add-spec-row"><?php _e('Add Row', 'woocommerce'); ?></a>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			</p>
		</div>

		<script>
			jQuery(document).ready(function($) {
				// Add row
				$('#add-spec-row').on('click', function(e) {
					e.preventDefault();
					var row = '<tr>' +
						'<td><input type="text" name="spec_label[]" value="" placeholder="e.g. Material" style="width:100%;" /></td>' +
						'<td><input type="text" name="spec_value[]" value="" placeholder="e.g. 22KT Gold" style="width:100%;" /></td>' +
						'<td><a href="#" class="button remove-spec-row">×</a></td>' +
						'</tr>';
					$('#product-specs-table tbody').append(row);
				});

				// Remove row
				$(document).on('click', '.remove-spec-row', function(e) {
					e.preventDefault();
					$(this).closest('tr').remove();
				});
				// ✅ Dynamically sync SKU field whenever the WooCommerce SKU input changes
				$('#_sku').on('input change', function() {
					var skuVal = $(this).val().trim();
					$('#spec-sku-value').val(skuVal !== '' ? skuVal : 'Not set');
				});
			});
		</script>
		<style>
			#product-specs-table th {
				font-weight: bold;
				text-align: left;
				padding: 10px;
			}

			#product-specs-table td {
				padding: 5px 10px;
			}

			.remove-spec-row {
				color: #a00 !important;
				border-color: #a00 !important;
			}

			.remove-spec-row:hover {
				background: #a00 !important;
				color: #fff !important;
			}
		</style>
	</div>
<?php
}
add_action('woocommerce_product_data_panels', 'specs_product_data_panel');

/**
 * Save the custom specifications data.
 */
function save_specs_product_data($post_id)
{
	if (isset($_POST['spec_label']) && isset($_POST['spec_value'])) {
		$labels = $_POST['spec_label'];
		$values = $_POST['spec_value'];
		$specs = array();

		for ($i = 0; $i < count($labels); $i++) {
			if (!empty($labels[$i]) && !empty($values[$i])) {
				$specs[] = array(
					'label' => sanitize_text_field($labels[$i]),
					'value' => sanitize_text_field($values[$i]),
				);
			}
		}

		if (!empty($specs)) {
			update_post_meta($post_id, '_product_specifications', $specs);
		} else {
			delete_post_meta($post_id, '_product_specifications');
		}
	} else {
		delete_post_meta($post_id, '_product_specifications');
	}
}
add_action('woocommerce_process_product_meta', 'save_specs_product_data');


/**
 * Automatically re-validate applied coupons on every cart update.
 * Ensures coupons are removed if criteria (like minimum spend) are no longer met.
 */
function ahnira_validate_cart_coupons($cart)
{
	if (is_admin() && !defined('DOING_AJAX')) {
		return;
	}


	static $running = false;
	if ($running) {
		return;
	}

	$running = true;
	$cart->check_cart_coupons();
	$running = false;
}
add_action('woocommerce_after_calculate_totals', 'ahnira_validate_cart_coupons', 10);

/**
 * Enqueue Media Scripts for Product Admin
 */
add_action('admin_enqueue_scripts', 'ahnira_admin_vto_scripts');
function ahnira_admin_vto_scripts($hook)
{
	if ('post.php' !== $hook && 'post-new.php' !== $hook)
		return;
	global $post;
	if ($post && 'product' === $post->post_type) {
		wp_enqueue_media();
	}
}
/**
 * Register Footer Menu Locations
 */
function ahnira_register_footer_menus()
{
	register_nav_menus(array(
		'footer-category' => __('Footer Category', 'storefront-child-ahnira'),
		'footer-services' => __('Footer Services', 'storefront-child-ahnira'),
		'footer-support' => __('Footer Support', 'storefront-child-ahnira'),
	));
}
add_action('after_setup_theme', 'ahnira_register_footer_menus', 20);

/**
 * Allow GLB and GLTF File Uploads in Media Library
 */
add_filter('upload_mimes', 'ahnira_allow_vto_mimes');
function ahnira_allow_vto_mimes($mimes)
{
	$mimes['glb'] = 'model/gltf-binary';
	$mimes['gltf'] = 'model/gltf+json';
	return $mimes;
}

/**
 * Add Multiple Image Fields to Menu Items
 */
add_action('wp_nav_menu_item_custom_fields', 'ahnira_add_menu_item_images', 10, 4);
function ahnira_add_menu_item_images($item_id, $item, $depth, $args)
{
	if ($depth !== 0)
		return; // Only for top-level items or adjust as needed. 
	// Actually user said "for each sub category", usually this means top-level categories have mega menus.

	$images_meta = get_post_meta($item_id, '_menu_item_images', true);
	// Support both old array format and new CSV format
	if (is_array($images_meta)) {
		$images = $images_meta;
	} elseif (!empty($images_meta)) {
		$images = explode(',', $images_meta);
	} else {
		$images = array();
	}
	$images = array_filter(array_map('absint', $images));
?>
	<div class="ahnira-menu-images-wrapper"
		style="margin-top: 10px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd;">
		<p class="description fw-bold"><?php _e('Mega Menu Images', 'storefront-child-ahnira'); ?></p>
		<div class="ahnira-menu-images-container">
			<?php foreach ($images as $img_id):
				$img_url = wp_get_attachment_image_url($img_id, 'thumbnail');
				if ($img_url): ?>
					<div class="ahnira-menu-image-item" style="display:inline-block; margin: 5px; position:relative;"
						data-id="<?php echo $img_id; ?>">
						<img src="<?php echo esc_url($img_url); ?>"
							style="width:50px; height:50px; object-fit:cover; border:1px solid #ccc;">
						<a href="#" class="ahnira-remove-menu-image"
							style="position:absolute; top:-5px; right:-5px; background:red; color:#fff; border-radius:50%; width:15px; height:15px; line-height:13px; text-align:center; font-size:10px; text-decoration:none;">×</a>
					</div>
			<?php endif;
			endforeach; ?>
		</div>
		<input type="hidden" name="ahnira_menu_image_<?php echo $item_id; ?>" class="ahnira-menu-images-input"
			value="<?php echo esc_attr(implode(',', $images)); ?>">
		<button type="button" class="button ahnira-add-menu-image"
			data-item-id="<?php echo $item_id; ?>"><?php _e('Add Image', 'storefront-child-ahnira'); ?></button>
	</div>
	<script>
		if (typeof ahnira_menu_img_init === 'undefined') {
			var ahnira_menu_img_init = true;
			jQuery(document).on('click', '.ahnira-add-menu-image', function(e) {
				e.preventDefault();
				var button = jQuery(this);
				var itemId = button.data('item-id');
				var container = button.siblings('.ahnira-menu-images-container');
				var frame = wp.media({
					title: 'Select Menu Image',
					button: {
						text: 'Add to Menu'
					},
					multiple: true
				});
				frame.on('select', function() {
					var selections = frame.state().get('selection');
					selections.each(function(attachment) {
						attachment = attachment.toJSON();
						var html = '<div class="ahnira-menu-image-item" style="display:inline-block; margin: 5px; position:relative;" data-id="' + attachment.id + '">' +
							'<img src="' + attachment.sizes.thumbnail.url + '" style="width:50px; height:50px; object-fit:cover; border:1px solid #ccc;">' +
							'<a href="#" class="ahnira-remove-menu-image" style="position:absolute; top:-5px; right:-5px; background:red; color:#fff; border-radius:50%; width:15px; height:15px; line-height:13px; text-align:center; font-size:10px; text-decoration:none;">×</a>' +
							'</div>';
						container.append(html);
					});
					updateItemImageInput(itemId);
				});
				frame.open();
			});

			jQuery(document).on('click', '.ahnira-remove-menu-image', function(e) {
				e.preventDefault();
				var item = jQuery(this).parent();
				var wrapper = item.closest('.ahnira-menu-images-wrapper');
				var itemId = wrapper.find('.ahnira-add-menu-image').data('item-id');
				item.remove();
				updateItemImageInput(itemId);
			});

			function updateItemImageInput(itemId) {
				var ids = [];
				var wrapper = jQuery('.ahnira-add-menu-image[data-item-id="' + itemId + '"]').closest('.ahnira-menu-images-wrapper');
				wrapper.find('.ahnira-menu-image-item').each(function() {
					ids.push(jQuery(this).data('id'));
				});
				wrapper.find('.ahnira-menu-images-input').val(ids.join(','));
			}
		}
	</script>
	<?php
}

/**
 * Save Menu Item Image Fields
 */
add_action('wp_update_nav_menu_item', 'ahnira_save_menu_item_images', 10, 2);
function ahnira_save_menu_item_images($menu_id, $menu_item_db_id)
{
	if (isset($_POST['ahnira_menu_image_' . $menu_item_db_id])) {
		// Save as CSV string for better reliability
		$images = sanitize_text_field($_POST['ahnira_menu_image_' . $menu_item_db_id]);
		update_post_meta($menu_item_db_id, '_menu_item_images', $images);
	} else {
		delete_post_meta($menu_item_db_id, '_menu_item_images');
	}
}

/**
 * Enqueue Media Scripts for Menu Page
 */
add_action('admin_enqueue_scripts', 'ahnira_admin_menu_scripts');
function ahnira_admin_menu_scripts($hook)
{
	if ('nav-menus.php' === $hook) {
		wp_enqueue_media();
	}
}

/**
 * Custom Menu Walker for Mega Menu
 */
class Ahnira_Mega_Menu_Walker extends Walker_Nav_Menu
{
	function start_lvl(&$output, $depth = 0, $args = null)
	{
		$indent = str_repeat("\t", $depth);
		if ($depth === 0) {
			global $ahnira_current_top_item_id;
			$images_meta = get_post_meta($ahnira_current_top_item_id, '_menu_item_images', true);

			// Handle both array (Legacy) and CSV string
			if (is_array($images_meta)) {
				$images = $images_meta;
			} elseif (!empty($images_meta)) {
				$images = explode(',', $images_meta);
			} else {
				$images = array();
			}
			$images = array_filter(array_map('absint', $images));
			$img_count = count($images);

			// Dynamic grid logic
			if ($img_count === 0) {
				$col_cat = "col-12";
				$col_img = "d-none";
			} elseif ($img_count === 1) {
				$col_cat = "col-xl-9 col-lg-8";
				$col_img = "col-xl-3 col-lg-4";
			} elseif ($img_count === 2) {
				$col_cat = "col-xl-8 col-lg-7";
				$col_img = "col-xl-4 col-lg-5";
			} else { // 3 or more
				$col_cat = "col-xl-4 col-lg-6";
				$col_img = "col-xl-8 col-lg-6";
			}

			$output .= "\n$indent<div class=\"ahnira-mega-menu\"><div class=\"container\"><div class=\"row\">\n";
			$output .= "$indent<ul class=\"sub-menu mega-menu-columns $col_cat\">\n";
		} else {
			$output .= "\n$indent<ul class=\"sub-menu\">\n";
		}
	}

	function end_lvl(&$output, $depth = 0, $args = null)
	{
		$indent = str_repeat("\t", $depth);
		if ($depth === 0) {
			$output .= "$indent</ul>\n";

			// Image area on the right
			global $ahnira_current_top_item_id;
			$images_meta = get_post_meta($ahnira_current_top_item_id, '_menu_item_images', true);

			if (is_array($images_meta)) {
				$images = $images_meta;
			} elseif (!empty($images_meta)) {
				$images = explode(',', $images_meta);
			} else {
				$images = array();
			}
			$images = array_filter(array_map('absint', $images));
			$img_count = count($images);

			if ($img_count > 0) {
				// Determine same column logic for output
				if ($img_count === 1) {
					$col_img = "col-xl-3 col-lg-4";
				} elseif ($img_count === 2) {
					$col_img = "col-xl-4 col-lg-5";
				} else {
					$col_img = "col-xl-8 col-lg-6";
				}

				$output .= "$indent<div class=\"mega-menu-images $col_img d-none d-lg-flex\">\n";
				foreach ($images as $img_id) {
					$img_url = wp_get_attachment_image_url($img_id, 'large');
					if ($img_url) {
						$output .= "<div class=\"mega-image-item\"><img src=\"" . esc_url($img_url) . "\" alt=\"Menu Image\"></div>\n";
					}
				}
				$output .= "$indent</div>\n";
			}

			$output .= "$indent</div></div></div>\n";
		} else {
			$output .= "$indent</ul>\n";
		}
	}

	function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
	{
		if ($depth === 0) {
			global $ahnira_current_top_item_id;
			$ahnira_current_top_item_id = $item->ID;
		}

		$indent = ($depth) ? str_repeat("\t", $depth) : '';
		$classes = empty($item->classes) ? array() : (array) $item->classes;

		if ($depth === 1 && $args->walker->has_children) {
			$classes[] = 'mega-column-header';
		}

		$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
		$class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

		$output .= $indent . '<li' . $class_names . '>';

		$atts = array();
		$atts['title'] = !empty($item->attr_title) ? $item->attr_title : '';
		$atts['target'] = !empty($item->target) ? $item->target : '';
		$atts['rel'] = !empty($item->xfn) ? $item->xfn : '';
		$atts['href'] = !empty($item->url) ? $item->url : '';

		$atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

		$attributes = '';
		foreach ($atts as $attr => $value) {
			if (!empty($value)) {
				$value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
	}
}

/**
 * Automatically generate a unique SKU for new products.
 */
add_action('woocommerce_before_product_object_save', 'ahnira_auto_generate_product_sku', 10, 2);
function ahnira_auto_generate_product_sku($product, $data_store)
{
	// Only generate if SKU is currently empty
	if (!empty($product->get_sku())) {
		return;
	}

	$product_name = $product->get_name();

	// Do not generate for auto-drafts or empty titles
	if (empty($product_name) || 'Auto Draft' === $product_name || 'auto-draft' === $product->get_status()) {
		return;
	}

	// 1. Base Prefix
	$base = 'AH00';

	// 2. Category Initials
	$category_initials = ahnira_get_category_initials($product);

	// 3. Series Initials (Optional)
	$series_initials = ahnira_get_series_initials($product);

	// 4. Product Name Initials
	$product_initials = ahnira_get_product_name_initials($product_name);

	// Combine prefix parts
	$sku_prefix = $base . $category_initials . $series_initials . $product_initials;

	// 5. Sequence Number
	$next_number = ahnira_get_next_sku_sequence($sku_prefix);
	$sequence_str = sprintf('%03d', $next_number);

	// 6. Complete SKU (ends with AH)
	$final_sku = $sku_prefix . $sequence_str . 'AH';

	$product->set_sku($final_sku);
}

/**
 * Determine the category initials for a product.
 */
function ahnira_get_category_initials($product)
{
	$category_ids = $product->get_category_ids();
	if (empty($category_ids)) {
		return 'XX';
	}

	// Use the first category assigned
	$term = get_term($category_ids[0], 'product_cat');
	if (empty($term) || is_wp_error($term)) {
		return 'XX';
	}

	$name = trim($term->name);
	$name_lower = strtolower($name);

	// Normalize plurals (remove trailing 's' if name is long enough)
	$normalized = $name_lower;
	if (strlen($name_lower) > 3 && substr($name_lower, -1) === 's') {
		$normalized = substr($name_lower, 0, -1);
	}

	// Direct mapping for common categories
	$mapping = array(
		'ring' => 'RG',
		'bracelet' => 'BG',
		'earring' => 'EG',
		'necklace' => 'NG',
		'pendant' => 'PT',
		'anklet' => 'AK',
		'bangle' => 'BL',
		'chain' => 'CH',
		'cufflink' => 'CL',
		'nosepin' => 'NP',
	);

	if (isset($mapping[$normalized])) {
		return $mapping[$normalized];
	}
	if (isset($mapping[$name_lower])) {
		return $mapping[$name_lower];
	}

	// Fallback for custom categories
	// If multiple words, take first letter of each word
	if (strpos($name, ' ') !== false || strpos($name, '-') !== false) {
		$words = preg_split('/[\s\-]+/', $name);
		$initials = '';
		foreach ($words as $word) {
			$initials .= substr($word, 0, 1);
		}
		return strtoupper(substr($initials, 0, 3));
	}

	// If single word, take first and last letter of the singular form
	$singular = $normalized;
	if (strlen($singular) >= 2) {
		return strtoupper(substr($singular, 0, 1) . substr($singular, -1));
	}

	return strtoupper(substr($name, 0, 2));
}

/**
 * Determine the series initials for a product.
 */
function ahnira_get_series_initials($product)
{
	$series_name = '';
	$product_id = $product->get_id();

	// 1. Check taxonomy (product_series or series)
	$series_terms = get_the_terms($product_id, 'product_series');
	if (empty($series_terms) || is_wp_error($series_terms)) {
		$series_terms = get_the_terms($product_id, 'series');
	}
	if (!empty($series_terms) && !is_wp_error($series_terms)) {
		$series_name = $series_terms[0]->name;
	}

	// 2. Check product attributes (series or pa_series)
	if (empty($series_name)) {
		$series_name = $product->get_attribute('series');
	}
	if (empty($series_name)) {
		$series_name = $product->get_attribute('pa_series');
	}

	// 3. Check custom fields (meta keys '_series' or 'series')
	if (empty($series_name)) {
		$series_name = get_post_meta($product_id, '_series', true);
	}
	if (empty($series_name)) {
		$series_name = get_post_meta($product_id, 'series', true);
	}

	if (empty($series_name)) {
		return '';
	}

	$series_name = trim($series_name);

	// Convert to initials
	if (strpos($series_name, ' ') !== false || strpos($series_name, '-') !== false) {
		$words = preg_split('/[\s\-]+/', $series_name);
		$initials = '';
		foreach ($words as $word) {
			$initials .= substr($word, 0, 1);
		}
		return strtoupper(substr($initials, 0, 3));
	}

	// For single word series, take first 2 letters
	if (strlen($series_name) >= 2) {
		return strtoupper(substr($series_name, 0, 2));
	}

	return strtoupper($series_name);
}

/**
 * Generate initials from a product name.
 */
function ahnira_get_product_name_initials($product_name)
{
	// Remove special characters, keep letters and spaces
	$cleaned = preg_replace('/[^A-Za-z0-9\s]/', '', $product_name);
	$words = preg_split('/\s+/', trim($cleaned));

	// Filter out common filler words
	$stopwords = array('and', 'with', 'for', 'the', 'a', 'of', 'in', 'or', 'by', 'to');
	$filtered_words = array();
	foreach ($words as $word) {
		if (!in_array(strtolower($word), $stopwords, true)) {
			$filtered_words[] = $word;
		}
	}

	if (empty($filtered_words)) {
		$filtered_words = $words;
	}

	$initials = '';
	foreach ($filtered_words as $word) {
		$initials .= substr($word, 0, 1);
	}

	$initials = strtoupper($initials);

	// Limit name initials to 3 characters max
	return substr($initials, 0, 3);
}

/**
 * Calculate the next increment number for the given SKU prefix.
 */
function ahnira_get_next_sku_sequence($prefix)
{
	global $wpdb;

	// Search for all SKUs starting with our prefix and ending with 'AH'
	$prefix_escaped = esc_sql($prefix);
	$query = "
		SELECT meta_value 
		FROM {$wpdb->postmeta} 
		WHERE meta_key = '_sku' 
		AND meta_value LIKE '{$prefix_escaped}%AH'
	";

	$existing_skus = $wpdb->get_col($query);

	if (empty($existing_skus)) {
		return 1;
	}

	$max_num = 0;
	$prefix_len = strlen($prefix);

	foreach ($existing_skus as $sku) {
		// The numeric string is located between the prefix and the trailing 'AH'
		// e.g. for SKU 'AH00RGSLCSR001AH' with prefix 'AH00RGSLCSR' (len 11)
		// substr( $sku, 11, -2 ) extracts '001'
		$num_str = substr($sku, $prefix_len, -2);
		if (is_numeric($num_str)) {
			$num_val = intval($num_str);
			if ($num_val > $max_num) {
				$max_num = $num_val;
			}
		}
	}

	return $max_num + 1;
}

/**
 * Admin action to bulk generate SKUs for existing products.
 * Triggered by adding ?ahnira_generate_existing_skus=1 or ?ahnira_generate_existing_skus=force to the URL in WP Admin.
 */
add_action('admin_init', 'ahnira_bulk_generate_existing_skus');
function ahnira_bulk_generate_existing_skus()
{
	if (!current_user_can('manage_options')) {
		return;
	}

	if (!isset($_GET['ahnira_generate_existing_skus'])) {
		return;
	}

	$mode = $_GET['ahnira_generate_existing_skus'];
	$force = ('force' === $mode);

	// Setup query for products
	$args = array(
		'limit' => -1,
		'return' => 'ids',
	);

	// If not forcing, only get products that currently have NO SKU
	if (!$force) {
		$args['sku'] = '';
	}

	$product_ids = wc_get_products($args);

	if (empty($product_ids)) {
		wp_die('No products found matching the criteria (either they already have SKUs or there are no products). Add ?ahnira_generate_existing_skus=force to regenerate all SKUs.', 'Bulk SKU Generation', array('back_link' => true));
	}

	$updated_count = 0;
	$skipped_count = 0;

	foreach ($product_ids as $product_id) {
		$product = wc_get_product($product_id);
		if (!$product) {
			continue;
		}

		// If force mode, we temporarily clear the SKU so the generator knows to generate it
		if ($force) {
			$product->set_sku('');
		}

		// Manually invoke our auto-generation function
		ahnira_auto_generate_product_sku($product, null);

		// Only save if a new SKU was successfully generated
		if (!empty($product->get_sku())) {
			$product->save();
			$updated_count++;
		} else {
			$skipped_count++;
		}
	}

	wp_die(sprintf('Success! Bulk SKU generation complete. Updated: %d products. Skipped: %d products (e.g. auto-drafts or empty titles).', $updated_count, $skipped_count), 'Bulk SKU Generation', array('back_link' => true));
}
add_filter('wpseo_opengraph_type', function ($type) {
	if (is_product())
		return 'product';
	return $type;
});
add_action('wp_head', function () {
	if (!is_front_page())
		return;
	$schema = [
		"@context" => "https://schema.org",
		"@type" => "Organization",
		"name" => "Ahnira",
		"url" => "https://ahnira.in",
		"logo" => ["@type" => "ImageObject", "url" => "https://ahnira.in/wp-content/uploads/2026/05/ahnira-logo.png"],
		"contactPoint" => [["@type" => "ContactPoint", "telephone" => "+91-95528-69200", "contactType" => "customer service", "availableLanguage" => ["English", "Hindi"]]],
		"sameAs" => ["https://www.instagram.com/ahnira.in/", "https://www.facebook.com/profile.php?id=61564700805191"],
		"address" => ["@type" => "PostalAddress", "streetAddress" => "86 C Mahadev Galli", "addressLocality" => "Kolhapur", "postalCode" => "416012", "addressCountry" => "IN"]
	];
	echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
});
add_action('wp_head', function () {
	if (!is_front_page())
		return;
	$schema = [
		"@context" => "https://schema.org",
		"@type" => "WebSite",
		"name" => "Ahnira",
		"url" => "https://ahnira.in",
		"potentialAction" => [
			"@type" => "SearchAction",
			"target" => ["@type" => "EntryPoint", "urlTemplate" => "https://ahnira.in/?s={search_term_string}"],
			"query-input" => "required name=search_term_string"
		]
	];
	echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
});
add_action('wp_head', function () {
	if (!is_product())
		return;
	global $post;
	$product = wc_get_product($post->ID);
	if (!$product)
		return;
	$img_id = $product->get_image_id();
	$img_url = $img_id ? wp_get_attachment_url($img_id) : '';
	$schema = [
		"@context" => "https://schema.org",
		"@type" => "Product",
		"name" => $product->get_name(),
		"image" => [$img_url],
		"description" => $product->get_description() ?: $product->get_short_description(),
		"sku" => $product->get_sku(),
		"brand" => ["@type" => "Brand", "name" => "Ahnira"],
		"material" => "925 Sterling Silver",
		"offers" => [
			"@type" => "Offer",
			"url" => get_permalink(),
			"priceCurrency" => "INR",
			"price" => $product->get_price(),
			"itemCondition" => "https://schema.org/NewCondition",
			"availability" => $product->is_in_stock() ? "https://schema.org/InStock" : "https://schema.org/OutOfStock",
			"seller" => ["@type" => "Organization", "name" => "Ahnira"]
		]
	];
	$avg = $product->get_average_rating();
	$count = $product->get_review_count();
	if ($avg > 0 && $count > 0) {
		$schema["aggregateRating"] = ["@type" => "AggregateRating", "ratingValue" => $avg, "reviewCount" => $count];
	}
	echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
});

add_action('wp_footer', 'add_custom_whatsapp_widget');
function add_custom_whatsapp_widget()
{
	// Hide widget on cart and checkout pages
	if (function_exists('is_cart') && (is_cart() || is_checkout())) {
		return;
	}

	echo '<div id="whatsapp-widget">
        <a href="https://api.whatsapp.com/send?phone=918010881801&text=Hi" target="_blank" class="whatsapp-floating-widget" data-html="true" data-placement="left">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>
	<style>
	#whatsapp-widget {
    bottom: 30px;
    position: fixed;
    z-index: 9999;
    right: 25px;
}
.single-product #whatsapp-widget{
bottom:100px;
}
#whatsapp-widget a.whatsapp-floating-widget {
    background-color: #25d366;
    color: #fff;
    border: none;
    cursor: pointer;
    width: 50px;
    height: 50px;
    text-align: center;
    box-shadow: 2px 2px 8px -3px #000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
}

#whatsapp-widget .fab.fa-whatsapp {
    font-size: 30px;
}
	</style>
	';
}
add_filter('loop_shop_per_page', 'custom_woocommerce_products_per_page', 9999);
function custom_woocommerce_products_per_page($per_page)
{
	return 12;
}
/**
 * ---------------------------------------------------------------
 * PERFORMANCE: identify whether an asset comes from parent theme,
 * child theme, a plugin, or an external/CDN host. Used below so we
 * can tell, per-file, exactly where a blocking CSS/JS is coming
 * from (parent Storefront vs child Ahnira vs plugin vs 3rd-party
 * CDN) instead of guessing.
 * ---------------------------------------------------------------
 */
// function ahnira_asset_origin($src)
// {
// 	if (empty($src)) return 'unknown';
// 	if (strpos($src, get_stylesheet_directory_uri()) === 0) return 'child-theme';
// 	if (strpos($src, get_template_directory_uri()) === 0) return 'parent-theme';
// 	if (strpos($src, content_url('/plugins')) === 0) return 'plugin';
// 	if (strpos($src, site_url()) === 0 || strpos($src, home_url()) === 0) return 'core/uploads';
// 	return 'external-cdn';
// }

/**
 * ---------------------------------------------------------------
 * PERFORMANCE: defer non-critical JS (parent + child + plugin).
 * Root cause found via trace: ~50 scripts/styles all fired
 * render-blocking in <head> at once on PDP, main thread sat idle
 * 76% of the time waiting on network, not compute. Deferring these
 * lets the browser paint before it downloads/executes them.
 *
 * Handles below cover: pixelyoursite (existing), Ahnira child
 * theme (bootstrap/fancybox/swiper/script/hc-sticky/footer/
 * swatch-variation/ion-range-slider/cart), and WooCommerce core
 * PDP scripts that were the tail-end blockers in the trace (zoom,
 * flexslider, photoswipe, single-product, add-to-cart, wishlist,
 * cart-fragments, sourcebuster, order-attribution).
 *
 * Do NOT add jquery-core here — everything below depends on it and
 * WordPress already prints jquery before its dependents in order,
 * deferring jquery itself is riskier and saves little.
 * ---------------------------------------------------------------
 */
// add_filter('script_loader_tag', function ($tag, $handle, $src) {

// 	// existing async list (pixelyoursite) — unchanged behaviour
// 	$async_targets = ['js-tld', 'pys', 'js-cookie-pys'];
// 	if (in_array($handle, $async_targets, true)) {
// 		$tag = str_replace(' defer src=', ' async src=', $tag);
// 		$tag = str_replace(' src=', ' async src=', $tag);
// 		return $tag;
// 	}

// 	// new: defer everything non-critical, from any origin (parent/child/plugin)
// 	$defer_targets = [
// 		// child theme (this functions.php)
// 		'bootstrap-js',
// 		'fancybox-cdn-js',
// 		'carousel-image-slider-swiper',
// 		'script',
// 		'hc-sticky-js',
// 		'footer-js',
// 		'swatch-variation',
// 		'ion-range-slider',
// 		'ion-range-slider-2',
// 		'cart-js',
// 		// WooCommerce core (PDP gallery + cart) — confirmed blocking in trace
// 		'zoom',
// 		'flexslider',
// 		'photoswipe',
// 		'photoswipe-ui-default',
// 		'wc-single-product',
// 		'wc-add-to-cart',
// 		'wc-add-to-cart-variation',
// 		'wc-cart-fragments',
// 		'woocommerce',
// 		'jquery-blockui',
// 		'js-cookie',
// 		'wc-blocks-vendors',
// 		'wc-blocks',
// 		'sourcebuster-js',
// 		'wc-order-attribution',
// 		// other plugins seen blocking in trace
// 		'customer-reviews-woocommerce',
// 		'colcade',
// 		'wp-hooks',
// 		'wp-i18n',
// 		'contact-form-7',
// 		'swv',
// 		'frequently-bought-together',
// 		'sidekart-frontend',
// 		'ti-woocommerce-wishlist',
// 		'wc-photoswipe',
// 		'header-cart',
// 		'storefront-navigation',
// 		'storefront-brands',
// 		'wpc-ajax-add-to-cart',
// 		'ajax-search-for-woocommerce',
// 		'comment-reply',
// 	];
// 	if (in_array($handle, $defer_targets, true)) {
// 		if (strpos($tag, ' defer') === false && strpos($tag, ' async') === false) {
// 			$tag = str_replace(' src=', ' defer src=', $tag);
// 		}
// 	}

// 	return $tag;
// }, 20, 3);

/**
 * ---------------------------------------------------------------
 * PERFORMANCE: load non-critical CSS without blocking first paint.
 * Uses the standard media="print" + onload swap trick — browser
 * fetches it at normal priority but does NOT block rendering on it,
 * then it's applied as soon as it arrives.
 *
 * fancybox.css was the single biggest blocker in the trace (4.2s,
 * VeryHigh priority, 3rd-party CDN). bootstrap/fontawesome/swiper
 * css were also render-blocking VeryHigh priority. Matched by $src
 * substring since most of these are plugin-registered, not
 * enqueued by this theme, so we can't rely on our own handles.
 *
 * DO NOT touch the main child theme stylesheet (style.css) here —
 * that carries above-the-fold layout and must stay blocking or PDP
 * will flash unstyled.
 * ---------------------------------------------------------------
 */
// add_filter('style_loader_tag', function ($tag, $handle, $href, $media) {

// 	$non_critical_src_matches = [
// 		'fancybox.css',                         // 3rd-party CDN, 4.2s blocker in trace — gallery lightbox only
// 		'bootstrap.min.css',                    // large utility framework, not needed for first paint
// 		'fontawesome/css/all.min.css',          // icon font, icons can pop in slightly late
// 		'swiper.bundle.min.css',                // carousel below/at fold, not LCP-critical
// 		'ion.rangeSlider.min.css',              // shop/category filter slider only
// 		'dashicons',                            // admin bar icons, irrelevant to storefront
// 		'block-library/style.min.css',          // Gutenberg core blocks, unused on PDP/shop
// 		'gutenberg-blocks.css',
// 		'wc-blocks.css',
// 		'badges.css',                           // customer-reviews-woocommerce
// 		'contact-form-7',
// 		'frequently-bought-together',
// 		'sidekart-frontend.css',
// 		'woo-pincode-checker-public.css',
// 		'1cc-product-checkout.css',             // woo-razorpay
// 		'photoswipe.min.css',
// 		'default-skin.min.css',
// 		'swatches.css',                         // variation-swatches-woo
// 		'ajax-search-for-woocommerce',
// 		'tinvwl',                               // ti-woocommerce-wishlist
// 		'brands.css',                           // storefront brands extension
// 		'woocommerce-ajax-filters',              // covers widget.css, select2, jquery-ui, Scrollbar, themes.css — 5 files, one match
// 		'flexi-image-slider',
// 		'elementor/assets/css/frontend.min.css',
// 		'essential-addons-for-elementor-lite',
// 		'fonts.googleapis.com',
// 		'ti-woocommerce-wishlist',                // covers both cached + plugin css version, one match
// 		'customer-reviews-woocommerce',
// 	];

// 	foreach ($non_critical_src_matches as $needle) {
// 		if (strpos($href, $needle) !== false) {
// 			$tag = '<link rel="stylesheet" id="' . esc_attr($handle) . '-css" href="' . esc_url($href) . '" media="print" onload="this.media=\'all\';this.onload=null;">' . "\n";
// 			// noscript fallback so CSS still applies if JS is disabled
// 			$tag .= '<noscript><link rel="stylesheet" href="' . esc_url($href) . '"></noscript>' . "\n";
// 			break;
// 		}
// 	}

// 	return $tag;
// }, 20, 4);
add_action('send_headers', function () {
	if (preg_match('/\.(webp|jpg|jpeg|png|woff2|css|js)$/', $_SERVER['REQUEST_URI'])) {
		header('Cache-Control: public, max-age=31536000, immutable');
	}
});
/**
 * ---------------------------------------------------------------
 * PERFORMANCE: catch raw <link> tags echoed directly into wp_head
 * by a plugin (NOT via wp_enqueue_style — those bypass
 * style_loader_tag entirely). Google Fonts (Source Sans Pro) is
 * injected this way, likely by Elementor or the parent theme's own
 * font loader, confirmed still VeryHigh/blocking after the normal
 * filter had no effect on it.
 * ---------------------------------------------------------------
 */
// add_action('wp_head', function () {
// 	ob_start();
// }, 0); // priority 0 = wrap everything else in wp_head

// add_action('wp_head', function () {
// 	$html = ob_get_clean();
// 	$html = preg_replace_callback(
// 		'/<link\s+[^>]*href=["\']([^"\']*fonts\.googleapis\.com[^"\']*)["\'][^>]*>/i',
// 		function ($m) {
// 			$href = esc_url($m[1]);
// 			return '<link rel="stylesheet" href="' . $href . '" media="print" onload="this.media=\'all\';this.onload=null;">'
// 				. '<noscript><link rel="stylesheet" href="' . $href . '"></noscript>';
// 		},
// 		$html
// 	);
// 	echo $html;
// }, PHP_INT_MAX); // fires last, after everything else has echoed into the buffer
function custom_preload_woocommerce_responsive_banner()
{
	// Only execute on Shop pages, Product Categories, or Product Tags
	if (is_product_category() || is_shop() || is_product_tag()) {

		$banner_image_id = 0;

		if (is_product_category()) {
			$queried_object = get_queried_object();
			$banner_image_id = get_term_meta($queried_object->term_id, 'thumbnail_id', true);
		} elseif (is_shop() || is_product_tag()) {
			$banner_image_id = get_post_thumbnail_id(wc_get_page_id('shop'));
		}

		// Fetch standard WordPress sizes based on the evaluated image ID
		$img_mobile  = wp_get_attachment_image_url($banner_image_id, 'medium_large'); // 768px wide
		$img_tablet  = wp_get_attachment_image_url($banner_image_id, 'large');        // 1024px wide
		$img_desktop = wp_get_attachment_image_url($banner_image_id, 'full');         // Master file

		// Core Fallback Path Adjustment (Corrected from child-theme directory to global uploads directory)
		if (!$img_desktop) {
			$upload_dir = wp_upload_dir();
			$fallback_url = $upload_dir['baseurl'] . '/2026/07/shop_banner-scaled_compress-1024x572.webp';
			$img_mobile = $img_tablet = $img_desktop = $fallback_url;
		}

		// Inject the high-priority responsive preloads matching your CSS media queries
		echo "\n" . '<!-- High-Priority Responsive Shop Banner Preloads -->' . "\n";

		if ($img_mobile) {
			echo '<link rel="preload" href="' . esc_url($img_mobile) . '" as="image" fetchpriority="high" media="(max-width: 768px)">' . "\n";
		}
		if ($img_tablet) {
			echo '<link rel="preload" href="' . esc_url($img_tablet) . '" as="image" fetchpriority="high" media="(min-width: 768.01px) and (max-width: 1200px)">' . "\n";
		}
		if ($img_desktop) {
			echo '<link rel="preload" href="' . esc_url($img_desktop) . '" as="image" fetchpriority="high" media="(min-width: 1200.01px)">' . "\n";
		}
	}
}
// Hooking at priority 1 outputs these instructions at the absolute top of your HTML <head> layout tree
add_action('wp_head', 'custom_preload_woocommerce_responsive_banner', 1);
function custom_preload_critical_fonts()
{
	// Get the base child theme URL path dynamically
	$theme_uri = get_stylesheet_directory_uri();

	echo "\n<!-- High-Priority Web Font Preloads -->\n";

	// 1. Preload Montserrat WOFF2 (Ensure you uploaded the converted .woff2 file here)
	echo '<link rel="preload" href="' . esc_url($theme_uri . '/assests/fonts/Montserrat-Regular.woff2') . '" as="font" type="font/woff2" crossorigin="anonymous">' . "\n";

	// 2. Preload FontAwesome Solid Icons (Update path if your child theme directory structure differs)
	echo '<link rel="preload" href="' . esc_url($theme_uri . '/assests/fontawesome/webfonts/fa-solid-900.woff2') . '" as="font" type="font/woff2" crossorigin="anonymous">' . "\n";

	// 3. Preload FontAwesome Brands Icons (Used for social media links)
	echo '<link rel="preload" href="' . esc_url($theme_uri . '/assests/fontawesome/webfonts/fa-brands-400.woff2') . '" as="font" type="font/woff2" crossorigin="anonymous">' . "\n";
}
// Priority 1 forces these links to print at the absolute top of your HTML source
add_action('wp_head', 'custom_preload_critical_fonts', 1);
function remove_email_from_review_form($fields)
{
	if (isset($fields['email'])) {
		unset($fields['email']);
	}
	if (isset($fields['url'])) {
		unset($fields['url']); // Optional: This also removes the website field
	}
	return $fields;
}
add_filter('comment_form_default_fields', 'remove_email_from_review_form');
add_filter('woocommerce_output_related_products_args', function ($args) {
	$args['posts_per_page'] = 8; // Number of related products
	// $args['columns'] = 4;       // Products per row
	return $args;
});


function ahnira_compressed_srcset($attachment_id)
{

	$metadata = wp_get_attachment_metadata($attachment_id);

	if (empty($metadata) || empty($metadata['file'])) {
		return '';
	}

	$upload_dir = wp_upload_dir();

	$base_dir = trailingslashit($upload_dir['basedir']);
	$base_url = trailingslashit($upload_dir['baseurl']);

	// Directory containing the original image
	$relative_file = $metadata['file'];
	$relative_dir  = dirname($relative_file);

	if ($relative_dir === '.') {
		$relative_dir = '';
	}

	$filesystem_dir = trailingslashit($base_dir . $relative_dir);
	$url_dir        = trailingslashit($base_url . $relative_dir);

	$srcset = array();

	// WordPress generated sizes
	if (! empty($metadata['sizes'])) {

		foreach ($metadata['sizes'] as $size) {

			if (empty($size['file']) || empty($size['width'])) {
				continue;
			}

			$original_file = $size['file'];

			// Convert:
			// image-300x300.webp
			// to:
			// image-300x300_compress.webp
			$compressed_file = preg_replace(
				'/(\.[^.]+)$/',
				'_compress$1',
				$original_file
			);

			$compressed_path = $filesystem_dir . $compressed_file;
			$compressed_url  = $url_dir . $compressed_file;

			// Only add it if the custom compressed file actually exists
			if (file_exists($compressed_path)) {

				$srcset[] = esc_url($compressed_url) . ' ' . intval($size['width']) . 'w';
			}
		}
	}

	// Add the original full compressed image
	$original_file = basename($metadata['file']);

	$compressed_original = preg_replace(
		'/(\.[^.]+)$/',
		'_compress$1',
		$original_file
	);

	$compressed_original_path = $filesystem_dir . $compressed_original;
	$compressed_original_url  = $url_dir . $compressed_original;

	if (file_exists($compressed_original_path)) {

		$original_width = ! empty($metadata['width'])
			? intval($metadata['width'])
			: 2560;

		$srcset[] = esc_url($compressed_original_url) . ' ' . $original_width . 'w';
	}

	return implode(', ', array_unique($srcset));
}
function ahnira_compressed_image_url($attachment_id)
{

	$metadata = wp_get_attachment_metadata($attachment_id);

	if (empty($metadata['file'])) {
		return wp_get_attachment_image_url($attachment_id, 'full');
	}

	$upload_dir = wp_upload_dir();

	$relative_file = $metadata['file'];
	$relative_dir  = dirname($relative_file);

	if ($relative_dir === '.') {
		$relative_dir = '';
	}

	$base_url = trailingslashit($upload_dir['baseurl']);

	$url_dir = trailingslashit($base_url . $relative_dir);

	$original_file = basename($metadata['file']);

	$compressed_file = preg_replace(
		'/(\.[^.]+)$/',
		'_compress$1',
		$original_file
	);

	$compressed_url = $url_dir . $compressed_file;

	return $compressed_url;
}
add_action('wp_head', function () {

	if (! is_shop() && ! is_product_category() && ! is_product_tag()) {
		return;
	}

	global $wp_query;

	if (empty($wp_query->posts)) {
		return;
	}

	$first_product_id = $wp_query->posts[0]->ID;

	$product = wc_get_product($first_product_id);

	if (! $product) {
		return;
	}

	$main_image_id = $product->get_image_id();

	if (! $main_image_id) {
		return;
	}

	$srcset = ahnira_compressed_srcset($main_image_id);
	$src    = ahnira_compressed_image_url($main_image_id);

	if (! $srcset || ! $src) {
		return;
	}

	echo '<link rel="preload" as="image" href="' . esc_url($src) . '" imagesrcset="' . esc_attr($srcset) . '" imagesizes="(max-width: 768px) 50vw, 300px">' . "\n";
}, 1);
/**
 * Remove Storefront icon stylesheet globally.
 */
// function ahnira_remove_storefront_icons()
// {
//     wp_dequeue_style('storefront-icons');
// }
// add_action('wp_enqueue_scripts', 'ahnira_remove_storefront_icons', 99);
add_action('wp_head', function () {
	echo '<link rel="preload" as="image" 
		href="https://ahnira.in/wp-content/uploads/2026/08/ahnira-logo-.webp" 
		imagesrcset="https://ahnira.in/wp-content/uploads/2026/08/ahnira-logo-.webp 510w, https://ahnira.in/wp-content/uploads/2026/08/ahnira-logo-300x120_compress.webp 300w, https://ahnira.in/wp-content/uploads/2026/08/ahnira-logo-416x166_compress.webp 416w, https://ahnira.in/wp-content/uploads/2026/08/ahnira-logo-64x26.webp 64w" 
		imagesizes="(max-width: 510px) 100vw, 510px" 
		fetchpriority="high">' . "\n";
}, 1);
add_action('wp_enqueue_scripts', function () {
	if (is_product()) {
		wp_dequeue_script('1cc_razorpay_checkout');
		wp_deregister_script('1cc_razorpay_checkout');
	}
}, 100); // priority 100 = runs AFTER plugin enqueues, so dequeue actually works
add_action('wp_footer', function () {
	if (is_product()) {
	?>
		<script>
			function loadMagicCheckout() {
				if (window.__mcLoaded) return;
				window.__mcLoaded = true;
				var s = document.createElement('script');
				s.src = 'https://checkout.razorpay.com/v1/magic-checkout.js';
				document.body.appendChild(s);
			}
			document.addEventListener('touchstart', function(e) {
				if (e.target.closest('.buy-now-button, .single_add_to_cart_button')) loadMagicCheckout();
			}, {
				passive: true
			});
			document.addEventListener('mouseenter', function(e) {
				if (e.target.closest && e.target.closest('.buy-now-button, .single_add_to_cart_button')) loadMagicCheckout();
			}, true);
		</script>
<?php
	}
});
add_action('wp_head', function () {
	echo '<style id="ahnira-critical-utils">
		.d-none{display:none!important}
		.d-flex{display:flex!important}
		@media (min-width:992px){
			.d-lg-none{display:none!important}
			.d-lg-flex{display:flex!important}
			.d-lg-block{display:block!important}
		}
		@media (min-width:768px){
			.d-md-none{display:none!important}
			.d-md-flex{display:flex!important}
			.d-md-block{display:block!important}
		}
	</style>' . "\n";
}, 1); // priority 1 = fires before everything else in wp_head
