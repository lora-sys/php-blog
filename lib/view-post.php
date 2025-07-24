<?php 
/*
* @param pdo 
*@param postid
@throw Exception
*/
function  getPostRow(PDO $pdo,$postid){
    $stmt =$pdo->prepare(
        '
        SELECT
        title,created_at,body
        FROM post
        WHERE id=:id
        '
    );
    if($stmt==false){
        throw new Exception("Error Processing Request", 1);
    }
    $result =$stmt->execute(array('id'=>$postid),);
    if($result===false){
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
//   1. 检查提交 (if ($_POST))。
//  2. 收集数据 ($commentData = ...)。
 //  3. 委托处理 (addCommentToPost(...))。
//   4. 检查结果 (if (!$errors))。
//   5. 成功则跳转，失败则（隐式地）继续执行以显示
 //     错误。 post-get-redirect模式，标准表单处理流程



?>