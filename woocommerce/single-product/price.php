<?php

/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

global $product;

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();

?>
<div class="price-wrap">
	<div class="price">
		<p><?php echo $product->get_price_html(); ?></p>
	</div>
	<div class="review-wrapper">
		<div class="woocommerce-product-rating">
			<?php if ($rating_count > 0) {
				echo wc_get_rating_html($average, $rating_count); // WPCS: XSS ok.
			} else { ?>
				<p class="stars">
					<span>
						<a class="star-1" href="#">1</a>
						<a class="star-2" href="#">2</a>
						<a class="star-3" href="#">3</a>
						<a class="star-4" href="#">4</a>
						<a class="star-5" href="#">5</a>
					</span>
				</p>
			<?php	}
			?>
			<?php if (comments_open()) : ?>
				<?php //phpcs:disable 
				?>
				<a href="#reviews" class="woocommerce-review-link" rel="nofollow">(<?php printf(_n('%s review', '%s reviews', $review_count, 'woocommerce'), '<span class="count">' . esc_html($review_count) . '</span>'); ?>)</a>
				<?php // phpcs:enable 
				?>
			<?php endif ?>
		</div>
	</div>
</div>
