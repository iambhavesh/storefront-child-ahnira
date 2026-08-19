<?php
/**
 * Login Form with OTP
 *
 * This template can be overridden by copying it to yourtheme/templates/mobile-login-woocommerce/xoo-ml-otp-login-form.php
 *
 * @version 2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<button type="button" class="xoo-ml-open-lwo-btn button btn <?php echo esc_attr( implode( ' ', $args['button_class'] ) ); ?> "><?php _e( 'Login with OTP', 'mobile-login-woocommerce' ); ?></button>

<div class="xoo-ml-lwo-form-placeholder custom-login-layout" <?php if( $args['login_first'] !== 'yes' ): ?> style="display: none;" <?php endif; ?> >

	<?php echo xoo_ml_get_phone_input_field( $args );  ?>

	<input type="hidden" name="redirect" value="<?php echo esc_attr( $args['redirect'] ); ?>">

	<input type="hidden" name="xoo-ml-login-with-otp" value="1">

    <div class="custom-login-buttons col-md-4 col-12">
	    <button type="submit" class="xoo-ml-login-otp-btn custom-btn-primary <?php echo esc_attr( implode( ' ', $args['button_class'] ) ); ?> "><?php _e( 'Login with OTP', 'mobile-login-woocommerce' ); ?></button>
	    <!-- <button type="button" class="xoo-ml-low-back custom-btn-secondary <?php // echo esc_attr( implode( ' ', $args['button_class'] ) ); ?>"><?php // _e( 'LOGIN WITH EMAIL & PASSWORD', 'mobile-login-woocommerce' ); ?></button> -->
    </div>

</div>
