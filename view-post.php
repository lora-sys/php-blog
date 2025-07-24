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
$errors = null;
if ($_POST)
{
    $action = isset($_GET['action']) ? $_GET['action'] : null;

    switch ($action)
    {
        case 'add-comment':
            $commentData = array(
                'name' => $_POST['comment_name'],
                'website' => $_POST['comment_website'],
                'text' => $_POST['comment_text'],
            );
            $errors = handleAddComment($pdo, $post_id, $commentData);
            if (!$errors) {
                redirectAndExit('view-post.php?post_id=' . $post_id);
            }
            break;
        case 'delete-comment':
            // Don't do anything if the user is not authorised
            if (isLoggedIn())
            {
                $deleteResponse = $_POST['delete-comment'];
                $keys = array_keys($deleteResponse);
                $deleteCommentId = $keys[0];
                deleteComment($pdo, $post_id, $deleteCommentId);
                redirectAndExit('view-post.php?post_id=' . $post_id);
            }
            break;
    }
}
else
{
    $commentData = array(
        'name' => '',
        'website' => '',
        'text' => '',
    );
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>博客应用 | <?php echo htmlspecialchars($row['title'], ENT_HTML5, 'utf-8') ?>
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
    by <?php echo htmlEscape($row['author_name']) ?>
    </div>
    <p>
        
        <?php echo convertNewLinesToParagraphs($row['body']) ?>
    </p>
    </div>

    <?php require 'templates/list-comment.php'?>
       <?php require 'templates/comment-form.php';?>
<p>
    <a href="index.php">
    返回主页
    </a>
</p>
</body> 



</html>