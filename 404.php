<?php

/**
 * The template for displaying 404 pages (not found).
 *
 * @package storefront
 */

get_header(); ?>

<div class="container">
	<div class="error-404 not-found">
		<div class="row ">
			<div class="col-12 d-flex flex-column align-items-center">
				<div class="error-image">
					<img src="https://minimog-4437.kxcdn.com/wp-content/themes/minimog/assets/images/404-image.png" alt="Not Found Image">
				</div>
				<h3 class="error-404-title"><?php esc_html_e('Oops!', 'storefront'); ?> </h3>
				<h4 class="error-404-sub-title"><?php esc_html_e('Page not found!', 'storefront'); ?></h4>
				<div class="error-buttons">
					<div class="error-404-button-wrapper">
						<a class="button"href="<?php echo get_home_url(); ?>/">
							<div class="button-content-wrapper">
								<span class="button-text">Go to Home</span>
							</div>
						</a>
					</div>
				</div>
			</div>
		</div><!-- .page-content -->
	</div><!-- .error-404 -->
</div><!-- #primary -->

<?php
get_footer();
