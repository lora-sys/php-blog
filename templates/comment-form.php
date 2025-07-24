<?php 
// 错误字符串
// 评论数组传递

?>
<?php // We'll use a rule-off for now, to separate page sections ?>
<hr/>
<?php //Report any errors in a bullet list?>
<?php if ($errors):?>
<div class="error box">
<ul>
    <?php foreach ($errors as $error):?>
        <li><?php echo $error?></li>
    <?php endforeach;?>
    </ul>
</div>


<?php endif;?>
<h3>添加你的评论</h3>

<form
    action="view-post.php?action=add-comment&amp;post_id=<?php echo $post_id?>"
    method="post"
    class="comment-form user-form"
>
<p>
    <label for="comment_name">
        名字
    </label>
    <input 
    type="text"
    id="comment_name"
    name="comment_name"
    value="<?php echo htmlEscape($commentData['name']) ?>"
    />
    </p>
<p>
<label for="comment_website">
网站
    </label>
<input
type="text"
id="comment_website"
name="comment_website"
value="<?php echo htmlEscape($commentData['website']) ?>"
/>
    </p>

    <p>
        <label for="comment_text">
            评论:
            </label>
            <textarea
            id="comment_text"
            name="comment_text"
            rows="8"
            cols="70"
            ><?php echo htmlEscape($commentData['text'])?>
                </textarea>
        </p>
<input
type="submit"
value="Submit comment"
/>
</form>