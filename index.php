<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Work out the path to the database, so SQLite/PDO can connect
require 'lib/common.php';
require 'lib/list-posts.php';
// Connect to the database, run a query, handle errors
session_start();
try{
$pdo=getPDO();
$posts=getAllPosts($pdo);
}catch(PDOException $e){
die('Database error: ' . $e->getMessage());
}

$notFound=isset($_GET['not-found']);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
     <title>博客</title>
     <?php  require 'templates/head.php'?>
        </head>
    <body>

    <header>
                <?php require 'templates/title.php' ?>
        </header>
        <?php if ($notFound) {?>
        <div class="error box">
            错误：找不到指定的博客文章。
        </div>
        <?php }?>

        <div class="post-list">
            <?php  foreach ($posts as $row): ?>
                <div class="post-synopsis">
                    <h2>
                        <?php echo htmlEscape($row['title']) ?>
                    </h2>
                    <div class="meta">
                        <?php echo convertSqlDate($row['created_at']) ?>
                        (<?php echo countCommentsForPost($pdo,$row['id']) ?> 评论)
                    </div>
                    <p>
                        <?php echo htmlEscape($row['body']) ?>
                    </p>
                    <div class="post-controls">
                        <ul>
                            <li>
                                <a href="view-post.php?post_id=<?php echo $row['id'] ?>" class="btn btn-primary">
                                    阅读全文
                                </a>
                            </li>
                            <?php if (isLoggedIn()): ?>
                                <li>
                                    <a href="edit-post.php?post_id=<?php echo $row['id'] ?>" class="btn btn-secondary">
                                        编辑
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>


    </body>
</html>