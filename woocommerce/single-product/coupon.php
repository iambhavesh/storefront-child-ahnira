<?php
global $woocommerce;
$coupon_posts = get_posts(array(
    'posts_per_page' => -1,
    'orderby' => 'name',
    'order' => 'asc',
    'post_type' => 'shop_coupon',
    'post_status' => 'publish',
));

$coupon_codes = []; // Initializing

foreach ($coupon_posts as $coupon_post) {
    $coupon_codes[] = $coupon_post->post_name;
}
if (!empty($coupon_codes)) { ?>
    <div class="coupons">
        <div class="coupons-list-title">
            <h4> Available Coupons</h4>
        </div>
        <div class="coupons-list">
            <?php foreach ($coupon_codes as $coupon) {
                $c = new WC_Coupon($coupon);
                ?>
                <div class="coupon-list-item ahnira-copy-coupon" data-code="<?php echo strtoupper($c->code); ?>">
                    <div class="coupon-tag-icon">
                        <i class="fa-solid fa-tag"></i>
                    </div>
                    <div class="coupon-content">
                        <div class="coupon-title">
                            <?php echo esc_html(strtoupper($c->code)); ?>
                        </div>
                        <div class="coupon-desc">
                            <?php echo $c->get_description() ?: 'Special Offer Available'; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-dark ms-auto"
                            style="font-size: 10px; padding: 2px 8px;"
                            onclick="navigator.clipboard.writeText('<?php echo esc_js(strtoupper($c->code)); ?>'); var btn = this; btn.innerText = 'COPIED!'; setTimeout(function() { btn.innerText = 'COPY'; }, 2000);">COPY</button>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
<?php }
