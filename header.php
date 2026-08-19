<?php

/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-DY80B3EXED"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());

		gtag('config', 'G-DY80B3EXED');
	</script>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
	<link href="https://fonts.gstatic.com" crossorigin="" rel="preconnect">
	<!-- <link rel="preconnect" href="https://fonts.googleapis.com"> -->
	<!-- <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@100;300;400;500;600&family=Jost&display=swap" rel="stylesheet"> -->
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php wp_body_open(); ?>

	<?php do_action('storefront_before_site'); ?>

	<div id="page" class="hfeed site">
		<?php do_action('storefront_before_header'); ?>

		<div id="page-top-bar" class="page-top-bar">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="top-bar-column-wrap d-flex justify-content-center align-items-center">
							<div class="top-bar-text">
								FLAT 5% OFF On Prepaid Order
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<header id="masthead" class="site-header" role="banner" style="<?php storefront_header_styles(); ?>">
			<div class="header-inner container">
				<!-- Mobile Menu Toggle -->
				<div class="mobile-menu-toggle d-xl-none">
					<button id="page-open-mobile-menu" class="toggle-btn page-open-mobile-menu" aria-label="Open Menu">
						<i class="fa-solid fa-bars"></i>
					</button>
				</div>

				<div class="header-logo-wrapper">
					<?php if (function_exists('the_custom_logo')) {
						the_custom_logo();
					} ?>
				</div>

				<div class="header-navigation-wrapper d-none d-xl-block">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container_class' => 'primary-navigation',
							'container' => 'nav',
							'menu_class' => 'menu d-flex justify-content-center',
							'walker' => new Ahnira_Mega_Menu_Walker(),
						)
					);
					?>

				</div>

				<div class="header-actions-wrapper">


					<div class="header-icons">
						<div class="header-search-icon d-none d-md-block">
							<!-- FiboSearch Shortcode -->
							<?php echo do_shortcode('[fibosearch]'); ?>
						</div>

						<div class="header-search-mobile d-md-none">
							<!-- Simple icon to toggle mobile search if needed, or just the search bar -->
							<?php echo do_shortcode('[fibosearch]'); ?>
						</div>
						<!-- Login/Account -->
						<a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>"
							class="header-action-btn account-btn d-none d-md-block"
							aria-label="<?php echo is_user_logged_in() ? 'My Account' : 'Login'; ?>">
							<i class="fa-regular fa-user"></i>
						</a>

						<!-- Link to Wishlist -->
						<div class="header-wishlist-wrapper">
							<?php
							$wishlist_html = do_shortcode('[ti_wishlist_products_counter]');
							$wishlist_html = str_replace('<span class="wishlist_products_counter_text"></span>', '<i class="fa-regular fa-heart"></i>', $wishlist_html);
							echo $wishlist_html;
							?>
						</div>

						<!-- Link to Cart -->
						<a href="<?php echo wc_get_cart_url(); ?>" class="header-action-btn cart-btn" aria-label="Cart">
							<span class="icon-wrapper">
								<i class="fa-solid fa-bag-shopping"></i>
								<div id="mini-cart-count" class="cart-count badge">
									<?php
									$items_count = WC()->cart->get_cart_contents_count();
									echo $items_count ? $items_count : '0';
									?>
								</div>
							</span>
						</a>
					</div>
				</div>
			</div>
		</header><!-- #masthead -->
		<div class="ahnira-menu-overlay"></div>

		<!-- Mobile Menu Container (Hidden by default, toggled via JS) -->
		<div id="page-mobile-main-menu" class="page-mobile-main-menu">

			<!-- Header: Logo + Close -->
			<div class="mmenu-header">
				<div class="mmenu-logo">
					<?php if (function_exists('the_custom_logo')) {
						the_custom_logo();
					} ?>
				</div>
				<button id="page-close-mobile-menu" class="page-close-mobile-menu" aria-label="Close Menu">
					<i class="fa-solid fa-xmark"></i>
				</button>
			</div>

			<!-- Profile / Account Section -->
			<div class="mmenu-profile">
				<?php if (is_user_logged_in()):
					$current_user = wp_get_current_user();
					$avatar = get_avatar($current_user->user_email, 48, '', '', array('class' => 'mmenu-avatar'));
				?>
					<div class="mmenu-profile-inner">
						<?php echo $avatar; ?>
						<div class="mmenu-profile-info">
							<span class="mmenu-profile-greeting">Hello,
								<?php echo esc_html($current_user->display_name); ?> 👋</span>
							<a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>"
								class="mmenu-profile-link">My Account</a>
						</div>
					</div>
				<?php else: ?>
					<div class="mmenu-profile-inner">
						<div class="mmenu-avatar-placeholder">
							<i class="fa-regular fa-user"></i>
						</div>
						<div class="mmenu-profile-info">
							<span class="mmenu-profile-greeting">Welcome, Guest!</span>
							<a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>"
								class="mmenu-profile-link">Login / Register</a>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<!-- Navigation Menu -->
			<div class="page-mobile-menu-content">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container_class' => 'mobile-menu',
					)
				);
				?>
			</div>

			<!-- Dynamic Coupons Section -->
			<?php
			$coupon_posts = get_posts(array(
				'posts_per_page' => 5,
				'orderby' => 'date',
				'order' => 'DESC',
				'post_type' => 'shop_coupon',
				'post_status' => 'publish',
			));
			if (!empty($coupon_posts)): ?>
				<div class="mmenu-coupons">
					<div class="mmenu-section-title">
						<i class="fa-solid fa-ticket"></i>
						<span>Offers &amp; Coupons</span>
					</div>
					<div class="mmenu-coupons-list">
						<?php foreach ($coupon_posts as $coupon_post):
							$c = new WC_Coupon($coupon_post->post_name);
							$discount_type = $c->get_discount_type();
							$amount = $c->get_amount();
							$desc = $c->get_description();

							if ($discount_type === 'percent') {
								$badge = (int) $amount . '% OFF';
							} elseif ($discount_type === 'fixed_cart' || $discount_type === 'fixed_product') {
								$badge = '₹' . (int) $amount . ' OFF';
							} else {
								$badge = 'OFFER';
							}
						?>
							<div class="mmenu-coupon-item ahnira-copy-coupon"
								data-code="<?php echo esc_attr(strtoupper($c->get_code())); ?>">
								<div class="mmenu-coupon-badge"><?php echo esc_html($badge); ?></div>
								<div class="mmenu-coupon-body">
									<div class="mmenu-coupon-code"><?php echo esc_html(strtoupper($c->get_code())); ?></div>
									<div class="mmenu-coupon-desc"><?php echo esc_html($desc ?: 'Apply at checkout'); ?></div>
								</div>
								<button type="button" class="mmenu-coupon-copy-btn" onclick="(function(btn, code){
								navigator.clipboard.writeText(code);
								btn.innerHTML='<i class=\'fa-solid fa-check\'></i>';
								btn.classList.add('copied');
								setTimeout(function(){ btn.innerHTML='COPY'; btn.classList.remove('copied'); }, 2000);
							})(this, '<?php echo esc_js(strtoupper($c->get_code())); ?>')">COPY</button>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<!-- Social Media Section -->
			<div class="mmenu-social">
				<div class="mmenu-section-title">
					<i class="fa-solid fa-share-nodes"></i>
					<span>Follow Us</span>
				</div>
				<div class="mmenu-social-links">
					<a href="https://www.instagram.com/ahnira.in/" target="_blank" class="mmenu-social-link" aria-label="Instagram">
						<svg viewBox="0 0 24 24" fill="currentColor">
							<path
								d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.981 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
						</svg>
					</a>
					<a href="https://www.facebook.com/profile.php?id=61564700805191" class="mmenu-social-link" target="_blank"
						aria-label="Facebook">
						<svg viewBox="0 0 24 24" fill="currentColor">
							<path
								d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" />
						</svg>
					</a>
				</div>
			</div>

		</div>

		<?php
		do_action('storefront_before_content');
		?>

		<div id="content" class="site-content" tabindex="-1">
			<?php
			if (is_checkout()) {
			?>
				<div class="container">
				<?php } elseif (is_front_page()) { ?>
					<div class="container-fluid px-0">
					<?php } else {
					?>
						<div class="container-fluid">
						<?php }

					do_action('storefront_content_top');
