<?php
// 添加新文章到数据库
function addPost(PDO $pdo, $title, $body, $userId){
    // 准备插入语句
    $sql = "
    INSERT INTO
    post
    (title, body, user_id, created_at)
    VALUES
    (:title, :body, :user_id, :created_at)
    ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false) {
        throw new Exception('无法准备文章插入语句');
    }
    $result = $stmt->execute(
        array(
            'title' => $title,
            'body' => $body,
            'user_id' => $userId,
            'created_at' => getSqlDateForNow(),
        )
    );
    if ($result === false) {
        throw new Exception('执行文章插入时出错');
    }
    return $pdo->lastInsertId();
}

// 编辑数据库中的已有文章
function editPost($pdo, $title, $body, $postId){
    // 准备更新语句
    $sql = "
    UPDATE 
    post
    SET
    title = :title,
    body = :body
    WHERE
    id = :postId
    ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false) {
        throw new Exception("无法准备文章更新语句");
    }
    $result = $stmt->execute(
        array(
            'title' => $title,
            'body' => $body,
            'postId' => $postId
        )
    );
    if ($result === false) {
        throw new Exception("执行文章更新时出错");
    }
    return true;
}
?>
