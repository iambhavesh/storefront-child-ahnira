<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
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

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => __( 'Billing address', 'woocommerce' ),
			'shipping' => __( 'Shipping address', 'woocommerce' ),
		),
		$customer_id
	);
} else {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => __( 'Billing address', 'woocommerce' ),
		),
		$customer_id
	);
}

$oldcol = 1;
$col    = 1;
?>

<div class="my-addresses-section">
	<p class="mb-4 text-muted">
		<?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ); ?>
	</p>

	<div class="row g-4 addresses">
		<?php foreach ( $get_addresses as $name => $address_title ) : ?>
			<?php $address = wc_get_account_formatted_address( $name ); ?>
			<div class="col-md-6 woocommerce-Address">
				<div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
					<div class="card-header bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
						<h5 class="mb-0 fw-bold"><?php echo esc_html( $address_title ); ?></h5>
						<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
							<?php echo $address ? esc_html__( 'Edit', 'woocommerce' ) : esc_html__( 'Add', 'woocommerce' ); ?>
						</a>
					</div>
					<div class="card-body p-4">
						<address class="mb-0 text-muted lh-lg">
							<?php echo $address ? wp_kses_post( $address ) : esc_html__( 'You have not set up this type of address yet.', 'woocommerce' ); ?>
						</address>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
	</div>
	<?php
endif;
