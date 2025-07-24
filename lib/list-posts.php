<?php

/**
 * 从数据库获取所有文章，用于后台列表
 * @param PDO $pdo
 * @return array
 */
function getAllPosts(PDO $pdo)
{
    $stmt = $pdo->query(
        'SELECT id, title, created_at, body
        FROM post
        ORDER BY created_at DESC'
    );
    if ($stmt === false) {
        throw new Exception('获取所有文章时发生错误');
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * 安全地删除一篇文章以及它下面的所有评论
 * 使用数据库事务来确保操作的原子性（要么都成功，要么都失败）
 * @param PDO $pdo
 * @param integer $postId
 * @return boolean
 */
function deletePost(PDO $pdo, $postId)
{
    // 1. 开始一个事务，相当于创建一个“保险箱”
    $pdo->beginTransaction();

    try {
        // 2. 先删除该文章下的所有子级评论
        $sql = "DELETE FROM comment WHERE post_id = :post_id";
        $stmt = $pdo->prepare($sql);
        if ($stmt === false) {
            throw new Exception('无法准备评论删除语句');
        }
        $stmt->execute(array('post_id' => $postId));

        // 3. 再删除父级文章本身
        $sql = "DELETE FROM post WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        if ($stmt === false) {
            throw new Exception('无法准备文章删除语句');
        }
        $stmt->execute(array('id' => $postId));

        // 4. 如果上面两步都成功了，就“提交”事务，让所有改动永久生效
        $pdo->commit();

    } catch (Exception $e) {
        // 5. 如果中间任何一步出错了，就“回滚”事务，撤销这个“保险箱”里发生的所有改动
        $pdo->rollBack();
        // 把错误信息重新抛出去，让调用者知道删除失败了
        throw $e;
    }

    return true;
}

?>
