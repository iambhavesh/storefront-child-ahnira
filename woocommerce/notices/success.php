<?php
/**
 * Show success messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/success.php.
 *
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $notices ) {
	return;
}

?>

<?php foreach ( $notices as $notice ) : ?>
	<div class="woocommerce-message custom-wc-notice notice-success"<?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> role="alert">
		<div class="wc-notice-inner">
			<i class="fa-solid fa-circle-check wc-notice-icon"></i>
			<div class="woocommerce-notice-content">
				<?php echo wc_kses_notice( $notice['notice'] ); ?>
			</div>
			<button class="woocommerce-notice-close" aria-label="Close">
				<i class="fa-solid fa-xmark"></i>
			</button>
			<div class="wc-notice-progress-bar"></div>
		</div>
	</div>
<?php endforeach; ?>
