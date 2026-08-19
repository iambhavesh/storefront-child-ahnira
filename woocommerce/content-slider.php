<?php

/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;
$id = $product->get_id();
// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}
?>

<div <?php wc_product_class('col-md-4 col-xl-3 product-col col-6 swiper-slide swatches-inserted', $product); ?>>

    <div class="product-wrapper">
        <div class="product-thumbnail">
            <div class="onsale-label">
                <?php echo woocommerce_show_product_sale_flash();
                ?>

            </div>
            <?php if (!$product->is_in_stock()) {
                ?>
                <div class="out-of-stock-wrapper">
                    <div class="out-of-stock-top">
                        <span>Sold out</span>
                    </div>
                </div>
            <?php } ?>
            <div class="thumbnail-wrapper">
                <div class="quick-view-wrapper">
                    <?php echo do_shortcode("[ti_wishlists_addtowishlist loop=yes]"); ?>
                    <?php echo do_shortcode('[woosq id="{' . $id . '}"]'); ?>
                </div>
                <?php
                do_action('woocommerce_before_shop_loop_item');

                ?>
                <div class="product-main-thumbnail">
                    <?php echo woocommerce_get_product_thumbnail('main-product-image'); ?>
                </div>
                <div class="product-hover-thumbnail">
                    <?php
                    $attachment_ids = $product->get_gallery_image_ids();

                    if (!empty($attachment_ids)) {
                        echo wp_get_attachment_image($attachment_ids[0], 'full');
                    } else {
                        echo woocommerce_get_product_thumbnail();
                    }
                    ?>
                </div>
                <?php
                do_action('woocommerce_after_shop_loop_item');
                ?>
            </div>
            <div class="product-action ">
                <?php woocommerce_template_loop_add_to_cart(); ?>
            </div>
        </div>
        <div class="product-info">
            <h3 class="product-title"><?php echo get_the_title() ?></h3>
            <div class="price">
                <?php
                echo woocommerce_template_loop_rating();
                echo woocommerce_template_loop_price();
                //do_action('woocommerce_after_shop_loop_item_title');
                ?>
            </div>
            <?php echo do_shortcode('[display_attributes id="' . $id . '"]'); ?>
        </div>
    </div>
</div>