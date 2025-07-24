<?php
/**
 * @var $pdo PDO
 * @var $post_id integer
 */
?>
<div class="comment-list">
    <h3><?php echo $row['comment_count'] ?> comments</h3>

    <form
        action="view-post.php?action=delete-comment&amp;post_id=<?php echo $post_id ?>"
        method="post"
        class="comment-list-form"
    >
        <?php foreach (getCommentsForPost($pdo, $post_id) as $comment): ?>
            <div class="comment">
                <div class="comment-meta">
                    Comment from
                    <?php echo htmlEscape($comment['name']) ?>
                    on
                    <?php echo convertSqlDate($comment['created_at']) ?>
                </div>
                <div class="comment-body">
                    <?php // This is already escaped ?>
                    <?php echo convertNewlinesToParagraphs($comment['text']) ?>
                </div>
                <?php if (isLoggedIn()): ?>
                    <input
                        type="submit"
                        name="delete-comment[<?php echo $comment['id'] ?>]"
                        value="Delete comment"
                    />
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </form>
</div>
