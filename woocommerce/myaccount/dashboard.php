<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */

defined('ABSPATH') || exit;

$current_user = wp_get_current_user();
?>

<div class="myaccount-dashboard">
	<div class="welcome-header mb-5">
		<h2 class="fw-bold"><?php printf(__('Welcome back, %s!', 'woocommerce'), esc_html($current_user->display_name)); ?></h2>
		<p class="text-muted"><?php _e('From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and edit your password and account details.', 'woocommerce'); ?></p>
	</div>

	<div class="row g-4 dashboard-cards">
		<div class="col-md-4">
			<a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>" class="card h-100 border-0 shadow-sm transition-up text-decoration-none">
				<div class="card-body text-center p-4">
					<div class="icon-circle bg-primary-light mb-3 mx-auto">
						<i class="fa-solid fa-shopping-bag text-primary"></i>
					</div>
					<h6 class="fw-bold mb-1 text-dark"><?php _e('Orders', 'woocommerce'); ?></h6>
					<p class="small text-muted mb-0"><?php _e('View your order history', 'woocommerce'); ?></p>
				</div>
			</a>
		</div>
		<div class="col-md-4">
			<a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-address')); ?>" class="card h-100 border-0 shadow-sm transition-up text-decoration-none">
				<div class="card-body text-center p-4">
					<div class="icon-circle bg-success-light mb-3 mx-auto">
						<i class="fa-solid fa-location-dot text-success"></i>
					</div>
					<h6 class="fw-bold mb-1 text-dark"><?php _e('Addresses', 'woocommerce'); ?></h6>
					<p class="small text-muted mb-0"><?php _e('Manage billing & shipping', 'woocommerce'); ?></p>
				</div>
			</a>
		</div>
		<div class="col-md-4">
			<a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>" class="card h-100 border-0 shadow-sm transition-up text-decoration-none">
				<div class="card-body text-center p-4">
					<div class="icon-circle bg-info-light mb-3 mx-auto">
						<i class="fa-solid fa-user-gear text-info"></i>
					</div>
					<h6 class="fw-bold mb-1 text-dark"><?php _e('Account Details', 'woocommerce'); ?></h6>
					<p class="small text-muted mb-0"><?php _e('Update your profile settings', 'woocommerce'); ?></p>
				</div>
			</a>
		</div>
	</div>
</div>
