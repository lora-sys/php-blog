<?php 
/*
* @param pdo 
*@param postid
@throw Exception
*/
function getPostRow(PDO $pdo, $postId)
{
    $stmt = $pdo->prepare(
        'SELECT
            p.title, p.created_at, p.body,
            u.name AS author_name,
            (SELECT COUNT(*) FROM comment WHERE comment.post_id = p.id) AS comment_count
        FROM
            post p
        JOIN
            user u ON p.user_id = u.id
        WHERE
            p.id = :id'
    );
    if ($stmt === false)
    {
        throw new Exception('There was a problem preparing this query');
    }
    $result = $stmt->execute(array('id' => $postId, ));
    if ($result === false)
    {
        throw new Exception('Failed to execute query for post.');
    }
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
}
/**
*write a comment form paricuar post
*@param $pdo
*@param $postId
*@param array $postdata
*@return array
*/

function writeCommentForm(PDO $pdo,$postId,array $commentData)
{
    $errors =array();
    // do some validation
    if (empty($commentData['name'])){
        $errors['name']='A name is required';
    }
    if(empty($commentData['text'])){
        $errors['text']='A comment is required';
    }
    if(!$errors){
    $sql="
    INSERT INTO comment
    (name,website,text,created_at,post_id)
    VALUES(:name,:website,:text,:created_at,:post_id)
    ";
    $stmt=$pdo->prepare($sql);
    if($stmt==false){
        throw new Exception('Cannot prepare statement to insert');
    }
    $result=$stmt->execute(
        array_Merge($commentData,array('post_id'=>$postId,'created_at'=>getSqlDateForNow()),)
    );
    if($result===false){
        $errorInfo=$stmt->errorInfo();
        if($errorInfo){
            $errors[]=$errorInfo[2];
        }
    }
}
 return $errors;
}

//添加评论
function handleAddComment(PDO $pdo, $postId, array $commentData)
{
    $errors = writeCommentForm(
        $pdo,
        $postId,
        $commentData
    );
    // If there are no errors, redirect back to self and redisplay
    if (!$errors)
    {
        redirectAndExit('view-post.php?post_id=' . $postId);
    }
    return $errors;
}

//删除评论 ，根据帖子id和评论id，他们是父子关系
function deleteComment(PDO $pdo, $postId, $commentId)
{
    // The comment id on its own would suffice, but post_id is a nice extra safety check
    $sql = "
        DELETE FROM
            comment
        WHERE
            post_id = :post_id
            AND id = :comment_id
    ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false)
    {
        throw new Exception('There was a problem preparing this query');
    }
    $result = $stmt->execute(
        array(
            'post_id' => $postId,
            'comment_id' => $commentId,
        )
    );
    return $result !== false;
}

?>