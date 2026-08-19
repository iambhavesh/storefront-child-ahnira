<?php

/**
 * Template used to display post content.
 *
 * @package storefront
 */

global $post;
?>

<div id="post-<?php the_ID(); ?>" <?php post_class('content-wrapper col mb-4'); ?>>
	<div class="post-wrapper border-0 shadow-none">
		<div class="post-thumbnail-wrapper mb-3">
			<div class="post-thumbnail overflow-hidden">
				<?php if (has_post_thumbnail($post->ID)) : ?>
					<?php $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large'); ?>
					<a href="<?php echo get_permalink() ?>" rel="bookmark" class="d-block overflow-hidden">
						<img src="<?php echo $image[0]; ?>" alt="<?php echo get_the_title(); ?>" class="img-fluid transition-img w-100" style="aspect-ratio: 3/2; object-fit: cover;">
					</a>
				<?php else :  ?>
					<a href="<?php echo get_permalink() ?>" rel="bookmark" class="d-block overflow-hidden">
						<img src="<?php echo get_stylesheet_directory_uri() . '/assests/images/empty-cart.webp'; ?>" alt="<?php the_title(); ?>" class="img-fluid transition-img w-100" style="aspect-ratio: 3/2; object-fit: cover;" />
					</a>
				<?php endif; ?>
			</div>
		</div>
		<div class="post-caption p-0">
			<div class="post-categories mb-2">
				<?php
				$categories = get_the_category();
				if (!empty($categories)) {
					foreach ($categories as $category) {
						echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 1px;">' . esc_html($category->name) . '</a> ';
					}
				}
				?>
			</div>
			<div class="post-title">
				<?php
				the_title(sprintf('<h4 class="entry-title fw-bold mb-0" style="font-size: 20px; line-height: 1.3;"><a href="%s" rel="bookmark" class="text-dark">', esc_url(get_permalink())), '</a></h4>');
				?>
			</div>
		</div>
	</div>
</div>