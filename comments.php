<?php

/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package storefront
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
	return;
}
?>


<?php
if (have_comments()) :
?>

	<div class="comments-wrap">
		<h2 class="comments-title">
			<?php
			printf(
				/* translators: 1: number of comments, 2: post title */
				esc_html(_nx('%1$s Comments', '%1$s Comments', get_comments_number(), 'comments title', 'storefront')),
				number_format_i18n(get_comments_number())
			);
			?></h2>

		<div class="comment-list-wrap">
			<ol class="comment-list">
				<?php
				wp_list_comments(array(
					'style'      => 'ol',
					'short_ping' => true,
					'callback' => 'better_comments'
				));
				?>
			</ol><!-- .comment-list -->
		</div>
	</div>
<?php endif;

if (!comments_open() && 0 !== intval(get_comments_number()) && post_type_supports(get_post_type(), 'comments')) :
?>
	<p class="no-comments"><?php esc_html_e('Comments are closed.', 'storefront'); ?></p>
<?php
endif; ?>
</div>

<?php

$args = apply_filters(
	'storefront_comment_form_args',
	array(
		'title_reply_before' => '<span id="reply-title" class="gamma comment-reply-title">',
		'title_reply_after'  => '</span>',
	)
);

comment_form(array(
	'title_reply' => 'Leave a Comment',
	'label_submit' => 'Submit',
));
?>