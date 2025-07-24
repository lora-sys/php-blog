<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Work out the path to the database, so SQLite/PDO can connect
require 'lib/common.php';
// Connect to the database, run a query, handle errors
session_start();
try{
$pdo=getPDO();
$stmt = $pdo->query(
    'SELECT
       id, title, created_at, body
    FROM

        post
    ORDER BY
        created_at DESC'
);
}catch(PDOException $e){
die('Database error: ' . $e->getMessage());
}
if ($stmt === false)
{
    throw new Exception('There was a problem running this query');
}
$notFound=isset($_GET['not-found']);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
     <title>Blog</title>
     <?php  require 'templates/head.php'?>
        </head>
    <body>

    <header>
                <?php require 'templates/title.php' ?>
        </header>
        <?php if ($notFound) {?>
        <div class="error box">
            Error: cannot find requested blog post.
        </div>
        <?php }?>

        <div class="post-list">
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="post-synopsis">
                    <h2>
                        <?php echo htmlEscape($row['title']) ?>
                    </h2>
                    <div class="meta">
                        <?php echo convertSqlDate($row['created_at']) ?>
                        (<?php echo countCommentsForPost($pdo,$row['id']) ?> comments)
                    </div>
                    <p>
                        <?php echo htmlEscape($row['body']) ?>
                    </p>
                    <div class="read-more">
                        <a
                            href="view-post.php?post_id=<?php echo $row['id'] ?>"
                        >Read more...</a>
                    </div>
                </div>
            <?php endwhile ?>
        </div>


    </body>
</html>