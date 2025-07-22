<?php
// Work out the path to the database, so SQLite/PDO can connect
$root = __DIR__;
$database = $root . '/data/data.sqlite';
$dsn = 'sqlite:' . $database;
// Connect to the database, run a query, handle errors
try{
$pdo = new PDO($dsn);
$stmt = $pdo->query(
    'SELECT
        title, created_at, body
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
?>
<!DOCTYPE html>
<html lang="en">
    <head>
     <meta charset="UTF-8">
     <title>Blog</title>
        </head>
    <body>

    <header>
                <h1>Blog title</h1>
        <p>This paragraph summarises what the blog is about.</p>
        </header>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <h2>
                <?php echo htmlspecialchars($row['title'], ENT_HTML5, 'UTF-8') ?>
            </h2>
            <div>
                <?php echo $row['created_at'] ?>
            </div>
            <p>
                <?php echo htmlspecialchars($row['body'], ENT_HTML5, 'UTF-8') ?>
            </p>
            <p>
                <a href="#">Read more...</a>
            </p>
        <?php endwhile ?>
    </body>
</html>