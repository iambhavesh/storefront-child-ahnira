<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
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

<ul class="woocommerce-error custom-wc-notice notice-error" role="alert">
	<?php foreach ( $notices as $notice ) : ?>
		<li<?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="wc-notice-inner">
				<i class="fa-solid fa-circle-xmark wc-notice-icon"></i>
				<div class="woocommerce-notice-content">
					<?php echo wc_kses_notice( $notice['notice'] ); ?>
				</div>
				<button class="woocommerce-notice-close" aria-label="Close">
					<i class="fa-solid fa-xmark"></i>
				</button>
				<div class="wc-notice-progress-bar"></div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
