<?php


get_header(); ?>
<div class="page-tile text-center py-5">
    <div class="page-title-bar-content">
        <div class="page-title-bar-heading">
            <h1 class="heading mb-3">
                <span style="font-size: 48px; font-weight: 500; letter-spacing: -1px;">
                    <?php
                    the_archive_title();
                    the_archive_description('<div class="taxonomy-description">', '</div>');
                    ?></span>
            </h1>
        </div>
        <div class="page-breadcrumbs d-flex justify-content-center">
            <?php echo woocommerce_breadcrumb(); ?>
        </div>
    </div>
</div>
<div id="page-content" class="page-content">
    <div class="container-fluid px-5">
        <div class="row col-xl-4 col-lg-3 col-md-2 col-1 g-4">
            <?php
            $current_cat = get_queried_object();
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => 12,
                'paged'          => $paged,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => $current_cat->slug,
                    ),
                ),
            );

            $products_query = new WP_Query($args);

            if ($products_query->have_posts()) :
                while ($products_query->have_posts()) :
                    $products_query->the_post();
                    wc_get_template_part('content', 'product');
                endwhile;
                wp_reset_postdata();

                // Simple pagination
                the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => __('&laquo; Previous', 'textdomain'),
                    'next_text' => __('Next &raquo;', 'textdomain'),
                ));

            else :
            ?>
                <div class="col-12 text-center py-5">
                    <div class="no-products-message">
                        <i class="fa-solid fa-cart-shopping fa-3x mb-3 text-muted"></i>
                        <h3 class="fw-bold"><?php _e('No products found', 'woocommerce'); ?></h3>
                        <p class="text-muted"><?php _e('Sorry, there are no products in this category at the moment.', 'woocommerce'); ?></p>
                        <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="btn btn-dark mt-3"><?php _e('Continue Shopping', 'woocommerce'); ?></a>
                    </div>
                </div>
            <?php
            endif;
            ?>
        </div>
    </div>
</div>
<?php
do_action('storefront_sidebar');
get_footer();
get_header(); ?>