<?php

/**
 * Single product short description
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/short-description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.3.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

global $post;

$short_description = apply_filters('woocommerce_short_description', $post->post_excerpt);

if (!$short_description) {
	return;
} ?>
<div class="short-description">
	<?php echo $short_description; ?>

</div>
<?php

$total_visitors = mt_rand(20, 50);

?>

<div id="live-viewing-visitors" class="live-viewing-visitors mb-4" data-settings='{"min":"20","max":"30","duration":10000,"labels":{"singular":"%s person is viewing this right now","plural":"%s people are viewing this right now"}}'>
	<i class=" icon animate-pulse fa-solid fa-eye" style="color: #000000;"></i>
	<div class="text">
		<?php echo sprintf(
			esc_html(_n('%s person is viewing this right now', '%s people are viewing this right now', $total_visitors, 'minimog')),
			'<span class="count">' . $total_visitors . '</span>'
		); ?>
	</div>
</div>
<?php echo do_shortcode('[wooct_product]');
?>