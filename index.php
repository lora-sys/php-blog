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
};
if ($stmt === false)
{
    throw new Exception('There was a problem running this query');
}
$notFound=isset($_GET['not-found']);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
     <meta charset="UTF-8">
     <title>Blog</title>
        </head>
    <body>

    <header>
                <?php require 'templates/title.php' ?>
        </header>
        <?php if ($notFound) {?>
        <div style="border: 1px solid #ff6666;padding:6px;">
            Error: cannot find requesed blog post.
        </div>
        <?php }?>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <h2>
                <?php echo htmlEscape($row['title']); ?>
            </h2>
            <div>
                Created on <?php echo convertSqlDate($row['created_at']); ?>
                (<?php echo countCommentsForPost($row['id'])?> comments)
            </div>
            <p>
                <?php echo htmlEscape($row['body']); ?>
            </p>
            <p>
                <a 
                href="view-post.php?post_id=<?php echo $row['id'] ?>"
                >README MORE...
                    </a>
            </p>
        <?php endwhile ?>
    </body>
</html>