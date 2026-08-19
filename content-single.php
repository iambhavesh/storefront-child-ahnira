<?php

/**
 * Template used to display post content on single pages.
 *
 * @package storefront
 */
?>
<div class="container single-blog-page">
	<article id="post-<?php the_ID(); ?>" <?php post_class('blog-wrapper'); ?>>

		<?php
		echo woocommerce_breadcrumb();
		//do_action('storefront_single_post_top');
		?>
		<div class="entry-header">
			<div class="entry-blog-categories mb-2">
				<?php
				$categories_list = get_the_category_list(__(', ', 'storefront'));
				?>
				<?php if ($categories_list): ?>
					<?php echo wp_kses_post($categories_list); ?>
				<?php endif; ?>
			</div>
			<?php
			if (is_single()) {
				the_title('<h1 class="entry-title">', '</h1>');
			} else {
				the_title(sprintf('<h2 class="alpha entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>');
			}
			?>
			<div class="entry-blog-meta mb-5">
				<div class="entry-blog-meta__inner">
					<div class="blog-author">
						<span>By</span>
						<?php the_author_posts_link(); ?>
					</div>
					<div class="blog-date">
						<span>on</span>
						<?php the_time('F jS, Y'); ?>
					</div>
				</div>
			</div>

			<div class="entry-content">
				<div class="blog-gallery">
					<?php do_action('storefront_post_content_before'); ?>
				</div>
				<?php
				the_content(
					sprintf(
						/* translators: %s: post title */
						__('Continue reading %s', 'storefront'),
						'<span class="screen-reader-text">' . get_the_title() . '</span>'
					)
				);

				do_action('storefront_post_content_after');

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . __('Pages:', 'storefront'),
						'after' => '</div>',
					)
				);
				?>
			</div>
			<div class="entry-footer py-4 border-bottom">
				<div class="entry-post-tags">
					<?php /* translators: used between list items, there is a space after the comma */
					$tags_list = get_the_tag_list('', __(', ', 'storefront'));
					if ($tags_list): ?>
						<div class="tags-label heading-color">Tags: </div>
						<div class="tagcloud">
							<?php echo wp_kses_post($tags_list); ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="entry-post-share">
					<div class="post-share style-01">
						<div class="share-label heading-color">
							Share: </div>
						<div class="share-media">
							<div class="share-list">
								<?php
								$post_url   = urlencode(get_permalink());
								$post_title = urlencode(get_the_title());
								?>
								<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $post_url; ?>" target="_blank" rel="noopener"><i class="fa-brands fa-facebook-f"></i></a>
								<a href="https://twitter.com/intent/tweet?text=<?php echo $post_title; ?>&url=<?php echo $post_url; ?>" target="_blank" rel="noopener"><i class="fa-brands fa-x-twitter"></i></a>
								<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $post_url; ?>&title=<?php echo $post_title; ?>" target="_blank" rel="noopener"><i class="fa-brands fa-linkedin-in"></i></a>
								<a href="https://tumblr.com/widgets/share/tool?canonicalUrl=<?php echo $post_url; ?>" target="_blank" rel="noopener"><i class="fa-brands fa-tumblr"></i></a>
								<a href="mailto:?subject=<?php echo $post_title; ?>&body=<?php echo $post_url; ?>"><i class="fa-regular fa-envelope"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="blog-nav-links mb-5">
				<?php
				$next_post = get_next_post();
				$prev_post = get_previous_post();

				if ($next_post || $prev_post): ?>
					<div class="nav-list d-flex align-items-center justify-content-between position-relative">
						<?php if (!empty($prev_post)): ?>
							<div class="nav-item prev">
								<div class="inner d-flex align-items-center">
									<span class="nav-arrow me-3"><span class="material-symbols-outlined">arrow_back</span></span>
									<div class="nav-content">
										<a href="<?php echo get_permalink($prev_post); ?>" rel="prev">
											<span class="nav-item--text prev-post d-block text-uppercase mb-1">Previous</span>
											<h6 class="mb-0"><?php echo get_the_title($prev_post); ?></h6>
										</a>
									</div>
								</div>
							</div>
						<?php endif; ?>

						<?php if ($next_post && $prev_post): ?>
							<div class="nav-divider"></div>
						<?php endif; ?>

						<?php if (!empty($next_post)): ?>
							<div class="nav-item next text-end">
								<div class="inner d-flex align-items-center justify-content-end">
									<div class="nav-content">
										<a href="<?php echo get_permalink($next_post); ?>" rel="next">
											<span class="nav-item--text next-post d-block text-uppercase mb-1">Next</span>
											<h6 class="mb-0"><?php echo get_the_title($next_post); ?></h6>
										</a>
									</div>
									<span class="nav-arrow ms-3"><span class="material-symbols-outlined">arrow_forward</span></span>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="blog-comments">
				<?php echo storefront_display_comments(); ?>
			</div>
	</article><!-- #post-## -->
</div>