<?php 
$root =__DIR__;
@$database =$root."/data/data.sqlite";
$dsn='sqlite:' .$database;
try{
$pdo =new PDO($dsn);
$stmt = $pdo->prepare(
'SELECT
title,created_at,body
FROM post
WHERE id=:id'
);}
catch(PDOException $e){
    die($e->getMessage());
}
if ($stmt ===false){
    throw new Exception('There was a problem running this query');
}
$result=$stmt->execute(array('id'=>1,));
if ($result === false){
    throw new Exception('There was a problem running this query');
}
$row= $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>A blog application | <?php echo htmlspecialchars($row['title'],ENT_HTML5,'utf-8') ?>
</title>
<meta http-equiv="Content-Type" content="text/html,charset=utf-8"/>
</head>    
<body>
<h1>Blog title</h1>
<p>This paragraph summaries what the blog is bout</p>
<h2>
    <?php echo htmlspecialchars($row['title'],ENT_HTML5,'utf-8') ?>

</h2>
<div>
    <?php echo $row['created_at'] ?>
    </div>
    <p>
        <?php echo htmlspecialchars($row['body'],ENT_HTML5,'utf-8')?>
        </p>
</body>



</html>