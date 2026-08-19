<?php

/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>

</div><!-- .col-full -->
</div><!-- #content -->

<?php do_action('storefront_before_footer'); ?>
<footer id="colophon" class="site-footer">
	<div class="container">
		<div class="row footer-main py-5">
			<!-- Column 1: Brand & Bio -->
			<div class="col-lg-3 col-md-6 mb-4 footer-col footer-about">
				<?php if (is_active_sidebar('footer-about')): ?>
					<?php dynamic_sidebar('footer-about'); ?>
				<?php else: ?>
					<h4 class="footer-title">AHNIRA</h4>
					<p class="footer-description">
						Enjoy the beauty of exquisite luxury your signature jewellery, crafted for those who appreciate
						timeless style.
					</p>
					<p>Ahnira is a premium jewelry brand by Tej Gold.</p>
					<div class="contact-info mt-4">
						<p>BIS HM/C-7591175521</p>
						<p><a href="mailto:support@ahnira.in">support@ahnira.in</a></p>
						<p><a href="tel:+918010881801">+918010881801</a></p>
					</div>
					<div class="social-icons mt-3">
						<a href="https://www.instagram.com/ahnira.in/" target="_blank" aria-label="Instagram">
							<svg viewBox="0 0 24 24" fill="currentColor">
								<path
									d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.981 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
							</svg>
						</a>
						<a href="https://www.facebook.com/profile.php?id=61564700805191" target="_blank"
							aria-label="Facebook">
							<svg viewBox="0 0 24 24" fill="currentColor">
								<path
									d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" />
							</svg>
						</a>
						<!-- <a href="#" target="_blank" aria-label="Pinterest">
							<svg viewBox="0 0 24 24" fill="currentColor">
								<path
									d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.966 1.406-5.966s-.359-.72-.359-1.781c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.261 7.929-7.261 4.162 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146 1.124.347 2.317.535 3.554.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z" />
							</svg>
						</a>
						<a href="#" target="_blank" aria-label="LinkedIn">
							<svg viewBox="0 0 24 24" fill="currentColor">
								<path
									d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 1 1 0-4.124 2.062 2.062 0 0 1 0 4.124zM7.119 20.452H3.554V9h3.565v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
							</svg>
						</a>
						<a href="#" target="_blank" aria-label="YouTube">
							<svg viewBox="0 0 24 24" fill="currentColor">
								<path
									d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
							</svg>
						</a> -->
					</div>
				<?php endif; ?>
			</div>

			<!-- Column 2: Category -->
			<div class="col-lg-3 col-md-6 mb-4 footer-col">
				<h4 class="footer-title">CATEGORY</h4>
				<?php
				wp_nav_menu(array(
					'theme_location' => 'footer-category',
					'container' => false,
					'menu_class' => 'footer-links list-unstyled',
					'fallback_cb' => '__return_false',
				));
				?>
			</div>

			<!-- Column 3: Services -->
			<div class="col-lg-3 col-md-6 mb-4 footer-col">
				<h4 class="footer-title">SERVICES/SUPPORT</h4>
				<?php
				wp_nav_menu(array(
					'theme_location' => 'footer-services',
					'container' => false,
					'menu_class' => 'footer-links list-unstyled',
					'fallback_cb' => '__return_false',
				));
				?>
			</div>

			<!-- Column 5: Newsletter -->
			<div class="col-lg-3 col-md-6 mb-4 footer-col footer-newsletter">

				<h4 class="footer-title">Need Help?</h4>
				<p class="newsletter-text">
					Everyone who shops from us is one of us, we will never ghost you. Reach out to us & we promise to
					help you out in best way possible.
				</p>
				<p class="newsletter-text">
					Email - <a href="mailto:support@ahnira.in">support@ahnira.in</a>
					<br>
					<strong>Mon - Sat (10 AM - 7 PM)</strong>
				</p>

			</div>
		</div>

		<!-- Footer Bottom Bar -->
		<div class="footer-bottom py-4">
			<div class="row align-items-center">
				<div class="col-md-6 copyright-text">
					<p>&copy; <?php echo date('Y'); ?>, Tej Gold. All Rights Reserved</p>
				</div>
				<div class="col-md-6 payment-methods text-md-end">
					<!-- <img src="/assests/images/payment-icons.png"
						alt="Payment Methods" class="payment-placeholder"> -->
				</div>
			</div>
		</div>
	</div>
</footer><!-- #colophon -->
<?php do_action('storefront_after_footer'); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>