<?php

/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

if (!defined('ABSPATH')) {
	exit;
}
$current_user = wp_get_current_user();

do_action('woocommerce_before_account_navigation');
?>
<div class="account-nav col-lg-3 col-md-4">
	<div class="account-nav-inner card border-0 shadow-sm p-4 h-100">
		<div class="my-account-profile text-center mb-4">
			<div class="my-avatar mb-3">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assests/images/profile.png'; ?>" class="rounded-circle shadow-sm" height="80" width="80" alt="Avatar">
			</div>
			<div class="profile-info">
				<p class="text-muted small mb-1"><?php _e('Hello,', 'woocommerce'); ?></p>
				<h5 class="my-name fw-bold mb-0"><?php echo $current_user->display_name; ?></h5>
			</div>
		</div>
		<nav class="woocommerce-account-navigation">
			<ul class="nav flex-column account-menu">
				<?php 
				$menu_icons = array(
					'dashboard'       => 'fa-solid fa-gauge-high',
					'orders'          => 'fa-solid fa-shopping-bag',
					'downloads'       => 'fa-solid fa-download',
					'edit-address'    => 'fa-solid fa-location-dot',
					'edit-account'    => 'fa-solid fa-user-gear',
					'wishlist'        => 'fa-solid fa-heart',
					'customer-logout' => 'fa-solid fa-right-from-bracket',
				);
				foreach (wc_get_account_menu_items() as $endpoint => $label) : 
					$icon = isset($menu_icons[$endpoint]) ? $menu_icons[$endpoint] : 'fa-solid fa-circle';
				?>
					<li class="nav-item <?php echo wc_get_account_menu_item_classes($endpoint); ?>">
						<a class="nav-link d-flex align-items-center py-2 px-3 mb-2 rounded transition" href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>">
							<i class="<?php echo $icon; ?> me-3 opacity-75"></i>
							<span><?php echo esc_html($label); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
	</div>
</div>
<?php do_action('woocommerce_after_account_navigation'); ?>