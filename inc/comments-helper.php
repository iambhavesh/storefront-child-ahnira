<?php
if (!function_exists('better_comments')) :
    function better_comments($comment, $args, $depth)
    {
?>
        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
            <div id="comment-<?php comment_ID() ?>" class="comment-wrap">
                <div class="comment-author vcard">
                    <?php
                    $avatar = get_avatar($comment->comment_author_email, 70, 'mysteryman');

                    echo $avatar;

                    // echo get_avatar(get_current_user_id(), 70); 
                    ?>
                    <!-- <img width="70" height="70" src="" sizes="(max-width: 70px) 100vw, 70px"> -->
                </div>
                <div class="comments-content">
                    <div class="meta">
                        <h6 class="fn"><?php echo get_comment_author() ?></h6>
                        <cite>
                            <?php
                            printf(/* translators: 1: date and time(s). */
                                esc_html__('%1$s at %2$s', '5balloons_theme'),
                                get_comment_date(),
                                get_comment_time()
                            ) ?>
                        </cite>
                    </div>
                    <div class="comment-text">
                        <p><?php comment_text() ?></p>
                    </div>

                    <div class="comment-footer">
                        <div class="comment-actions">
                            <?php echo comment_reply_link(array_merge($args, array(
                                'add_below' => 'comment',
                                'depth'     => $depth,
                                'max_depth' => $args['max_depth'],
                                'before'    => '<div class="reply">',
                                'after'     => '</div>'
                            ))); ?>
                        </div>
                    </div>
                </div>
            </div>
        </li>

<?php
    }
endif;
