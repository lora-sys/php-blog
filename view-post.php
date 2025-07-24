<?php
require_once 'lib/common.php';
require_once 'lib/view-post.php';
// get the post id
// get 或得页面信息
session_start();
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
} else {
    $post_id = 0;
}


$pdo=getPDO();
$row=getPostRow($pdo, $post_id);
if(!$row){
    redirectAndExit('index.php?not-found=1');
}
$errors=null;
if($_POST)
{
    $commentData=array(
        'name'=>$_POST['comment_name'],//超全局数组
        'website'=>$_POST['comment_website'],
        'text'=>$_POST['comment_text'],
    );
    $errors=writeCommentForm(
        $pdo,
        $post_id,
        $commentData
    );
    // 没有错，就重定向会自己，防止重复提交表单
    if(!$errors){
        redirectAndExit('view-post.php?post_id='.$post_id);
    }
}else{
    $commentData=array(
        'name'=>'',
        'website'=>'',
        'text'=>'',
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>A blog application | <?php echo htmlspecialchars($row['title'], ENT_HTML5, 'utf-8') ?>
</title>
<?php require 'templates/head.php'?>
</head>    
<body>
<?php require "templates/title.php"  ?>
<div class="post">
<h2>
    <?php echo htmlEscape($row['title']) ?>

</h2>
<div class="date">
    <?php echo convertSqlDate($row['created_at'])?>
    </div>
    <p>
        
        <?php echo convertNewLinesToParagraphs($row['body']) ?>
    </p>
    </div>

    <div class="comment-list">
        <h3><?php echo countCommentsForPost($pdo, $post_id) ?> comments</h3>
        <?php foreach (getCommentsForPost($pdo, $post_id) as $comment):    ?>
        <?php // For now, we'll use a horizontal rule-off to split it up a bit ?>
         <hr/>
         <div class="comment">
            <div class="comment-meta">
                Comment from
                <?php echo htmlEscape($comment['name']) ?>
                on --
                <?php echo convertSqlDate($comment['created_at'])?>
            </div>
        <div class="comment-body">
          <?php echo htmlEscape($comment['text']) ?>      
        </div>
       <?php endforeach; ?>
    </div>
       <?php require 'templates/comment-form.php';?>
<p>
    <a href="index.php">
    Back to the home page
    </a>
</p>
</body> 



</html>