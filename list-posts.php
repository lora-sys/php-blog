<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'lib/common.php';
require_once 'lib/list-posts.php'; // 确保加载了定义 deletePost 的文件
session_start();

// 权限检查
if (!isLoggedIn()) {
    redirectAndExit('index.php');
}

$pdo = getPDO();

// 处理删除请求
if ($_POST) {
    // 检查 'delete-post' 是否存在于POST数据中
    if (isset($_POST['delete-post'])) {
        $deleteResponse = $_POST['delete-post'];
        // 获取被点击的那个按钮的ID
        $keys = array_keys($deleteResponse);
        $deleteId = $keys[0];
        if ($deleteId) {
            deletePost($pdo, $deleteId);
            // 修正了这里的拼写错误
            redirectAndExit('list-posts.php');
        }
    }
}

// 获取所有文章数据
$posts = getAllPosts($pdo);

?>
<!DOCTYPE html>
<html>
<head>
    <title>博客后台 | 文章列表</title>
    <?php require 'templates/head.php' ?>
</head>
<body>
    <?php require 'templates/top-menu.php' ?>
    <h1>文章列表</h1>
    <p>您共有 <?php echo count($posts) ?> 篇文章。</p>
    <p>在这里您可以编辑或删除文章。</p>
    <form method="post">
        <table id="post-list">
            <thead>
                <tr>
                    <th>标题</th>
                    <th>创建日期</th>
                    <th colspan="2">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $row): ?>
                    <tr>
                        <td><?php echo htmlEscape($row['title']) ?></td>
                        <td>
                            <?php // 修正了这里的变量名错误 ?>
                            <?php echo convertSqlDate($row['created_at']) ?>
                        </td>
                        <td>
                            <?php // 补全了“编辑”链接的文字 ?>
                            <a href="edit-post.php?post_id=<?php echo $row['id'] ?>">编辑</a>
                        </td>
                        <td>
                            <input
                                type="submit"
                                name="delete-post[<?php echo $row['id'] ?>]"
                                value="删除"
                                onclick="return confirm('您确定要删除这篇文章吗？删除后无法恢复。')"
                            />
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</body>
</html>
