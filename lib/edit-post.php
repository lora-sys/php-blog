<?php
function addPost(PDO $pdo,$title,$body,$userId){

    //插入语句
    $sql="
    INSERT INTO
    post
    (title,body,user_id,created_at)
    VALUES
    (:title,:body,:user_id,:created_at)
    ";
    $stmt=$pdo->prepare($sql);
    if($stmt===false){
        throw new Exception("Could not prepare statement");

    }
    $result=$stmt->execute(
        array(
            'title'=>$title,
            'body'=>$body,
            'user_id'=>$userId,
            'created_at'=>getSqlDateForNow(),
        )
    );
    if($result===false){
        throw new Exception("Error Processing Request", 1);
        
    }
  return $pdo->lastInsertId();
}



function editPost($pdo,$title,$body,$postId){
    //插入语句
    $sql="
    UPDATE 
    post
    SET
    title=:title,
    body=:body
    WHERE
    id=:postId
    ";
    $stmt=$pdo->prepare($sql);
    if($stmt===false){
        throw new Exception("
        Error Processing Request
        ", 1);}

        $result=$stmt->execute(
            array(
                'title'=>$title,
                'body'=>$body,
                'postId'=>$postId
            )
        );
        if($result===false){
            throw new Exception("
            Error Processing Request
            ", 1);
        }
        return true;
}



?>