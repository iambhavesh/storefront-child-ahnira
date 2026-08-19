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
        <div class="row row-cols-xl-4 row-cols-lg-3 row-cols-md-2 row-cols-1 g-4">
            <?php
            if (have_posts()) :

                get_template_part('loop');

            else :

                get_template_part('content', 'none');

            endif;
            ?>
        </div>
    </div>
</div>
<?php
do_action('storefront_sidebar');
get_footer();
get_header(); ?>