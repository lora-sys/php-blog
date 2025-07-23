<?php 
// 错误字符串
// 评论数组传递

?>
<?php // We'll use a rule-off for now, to separate page sections ?>
<hr/>
<?php //Report any errors in a bullet list?>
<?php if ($errors):?>
<div style="border:1px solid #ff6666;padding:6px">
<ul>
    <?php foreach ($errors as $error):?>
        <li><?php echo $error?></li>
    <?php endforeach;?>
    </ul>
</div>


<?php endif;?>
<h3>Add your comment</h3>
<form method="post">
<p>
    <label for="comment_name">
        Name
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
Website
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
            Comment:
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