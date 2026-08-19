<?php
/**
 * Show info messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/notice.php.
 *
 * @package WooCommerce\Templates
 * @version 10.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $notices ) {
	return;
}

?>

<?php foreach ( $notices as $notice ) : ?>
	<div class="woocommerce-info custom-wc-notice notice-info"<?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> role="status">
		<div class="wc-notice-inner">
			<i class="fa-solid fa-circle-info wc-notice-icon"></i>
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
