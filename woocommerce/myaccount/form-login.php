<?php

/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<div class=" login-wraper container mt-5 justify-content-center d-flex">
	<div class="col-12 col-md-4">
		<?php do_action('woocommerce_before_customer_login_form'); ?>

		<!-- Pills navs -->
		<ul class="nav nav-pills nav-justified mb-3 ms-0" id="pills-tab" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab" aria-controls="pills-login" aria-selected="true">Login</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register" type="button" role="tab" aria-controls="pills-register" aria-selected="false">Register</button>
			</li>
		</ul>
		<!-- Pills navs -->
		<!-- Pills content -->
		<div class="tab-content " id="pills-tabContent">
			<div class="tab-pane fade show active" id="pills-login" role="tabpanel" aria-labelledby="tab-login">
				<form class="woocommerce-form woocommerce-form-login login" method="post">
					<?php do_action('woocommerce_login_form_start'); ?>
					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="username"><?php esc_html_e('Username or email address', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" />
					</p>
					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="password"><?php esc_html_e('Password', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
						<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
					</p>
					<?php do_action('woocommerce_login_form'); ?>
					<p class="form-row">
						<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
							<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e('Remember me', 'woocommerce'); ?></span>
						</label>
						<?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
						<button type="submit" class="woocommerce-button button woocommerce-form-login__submit<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="login" value="<?php esc_attr_e('Log in', 'woocommerce'); ?>"><?php esc_html_e('Log in', 'woocommerce'); ?></button>
					</p>
					<p class="woocommerce-LostPassword lost_password">
						<a href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php esc_html_e('Lost your password?', 'woocommerce'); ?></a>
					</p>
					<?php do_action('woocommerce_login_form_end'); ?>
				</form>
			</div>
			<div class="tab-pane fade" id="pills-register" role="tabpanel" aria-labelledby="tab-register">
				<!-- <div class="text-center mb-3">
					<p>Sign in with:</p>
					<button type="button" class="btn   btn-floating mx-1">
						<i class="fab fa-facebook-f"></i>
					</button>

					<button type="button" class="btn   btn-floating mx-1">
						<i class="fab fa-google"></i>
					</button>

					<button type="button" class="btn   btn-floating mx-1">
						<i class="fab fa-twitter"></i>
					</button>

					<button type="button" class="btn   btn-floating mx-1">
						<i class="fab fa-github"></i>
					</button>
				</div> -->

				<!-- <p class="text-center">or:</p> -->
				<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action('woocommerce_register_form_tag'); ?>>

					<?php do_action('woocommerce_register_form_start'); ?>

					<?php if ('no' === get_option('woocommerce_registration_generate_username')) : ?>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="reg_username"><?php esc_html_e('Username', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
							<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" /><?php // @codingStandardsIgnoreLine 
																																																																			?>
						</p>

					<?php endif; ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="reg_email"><?php esc_html_e('Email address', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
						<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo (!empty($_POST['email'])) ? esc_attr(wp_unslash($_POST['email'])) : ''; ?>" /><?php // @codingStandardsIgnoreLine 
																																																															?>
					</p>

					<?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="reg_password"><?php esc_html_e('Password', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
							<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
						</p>

					<?php else : ?>

						<p><?php esc_html_e('A link to set a new password will be sent to your email address.', 'woocommerce'); ?></p>

					<?php endif; ?>

					<?php do_action('woocommerce_register_form'); ?>

					<p class="woocommerce-form-row form-row">
						<?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
						<button type="submit" class="woocommerce-Button custom-btn-primary woocommerce-button button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?> woocommerce-form-register__submit" name="register" value="<?php esc_attr_e('Register', 'woocommerce'); ?>"><?php esc_html_e('Register', 'woocommerce'); ?></button>
					</p>

					<?php do_action('woocommerce_register_form_end'); ?>

				</form>
			</div>
		</div>
		<!-- Pills content -->

		<?php do_action('woocommerce_after_customer_login_form'); ?>
	</div>
</div>