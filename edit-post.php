<?php
// 1. 基础设置：开启错误显示，加载所有必需的库文件
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'lib/common.php';
require_once 'lib/edit-post.php';
require_once 'lib/view-post.php';
session_start();

// 2. 权限检查：未登录的用户不允许访问，直接跳转回首页
if (!isLoggedIn()) {
    redirectAndExit('index.php');
}

// 3. 区分模式：从URL获取 post_id，这是区分“新建模式”和“编辑模式”的关键
//    必须在所有逻辑开始前就定义好。如果URL中有 post_id，则$postId有值；否则为null。
$postId = isset($_GET['post_id']) ? $_GET['post_id'] : null;

// 4. 初始化变量：为标题、正文和错误消息准备好空的容器
$title = '';
$body = '';
$errors = array();
$pdo = getPDO();

// 5. 编辑模式下的数据加载：如果$postId有值（即我们在编辑一篇文章）
if ($postId) {
    // 通过 postId 从数据库获取文章数据
    $post = getPostRow($pdo, $postId);
    // 安全检查：如果根据ID找不到文章，也跳转回首页
    if (!$post) {
        redirectAndExit('index.php');
    }
    // 把从数据库读出的旧数据，填充到变量中，以便稍后在表单中显示出来
    $title = $post['title'];
    $body = $post['body'];
}

// 6. 处理表单提交：只在用户点击了提交按钮后（即$_POST不为空时），才执行这段逻辑
if ($_POST) {
    // 获取用户在表单中输入的（可能是新的）数据
    $title = $_POST['post-title'];
    $body = $_POST['post-body'];

    // 验证数据：确保标题和内容不为空
    if (!$title) {
        $errors[] = '标题是必填的';
    }
    if (!$body) {
        $errors[] = '内容是必填的';
    }

    // 如果没有验证错误，就执行数据库操作
    if (!$errors) {
        $userId = getAuthUserId($pdo);

        // 再次根据 $postId 是否有值，决定是“更新”还是“新建”
        if ($postId) {
            // 编辑模式：调用 editPost 函数来更新数据库
            editPost($pdo, $title, $body, $postId);
        } else {
            // 新建模式：调用 addPost 函数来插入新记录
            $postId = addPost($pdo, $title, $body, $userId);
            if ($postId === false) {
                $errors[] = '数据库操作失败';
            }
        }
    }

    // 7. 操作后跳转：如果整个过程没有任何错误，就跳转到新创建或刚编辑完的文章的查看页面
    if (!$errors) {
        redirectAndExit('view-post.php?post_id=' . $postId);
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>博客后台 | <?php echo $postId ? '编辑文章' : '新建文章' ?></title>
        <?php require 'templates/head.php'; ?>
    </head>
    <body>
        <?php require 'templates/top-menu.php'; ?>
        <?php if(isset($_GET['post_id'])):?>
            <h1>编辑文章</h1>
            <?php else:?>
                <h1>新建文章</h1>
                <?php endif;?>

        <?php // 如果$errors数组不为空，就把它里面的错误信息逐条显示出来 ?>
        <?php if ($errors): ?>
            <div class="error box">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" class="post-form user-form">
            <div>
                <label for="post-title">标题:</label>
                <input
                    id="post-title"
                    name="post-title"
                    type="text"
                    value="<?php echo htmlEscape($title) ?>"
                />
            </div>
            <div>
                <label for="post-body">正文:</label>
                <textarea
                    id="post-body"
                    name="post-body"
                    rows="12"
                    cols="70"
                ><?php echo htmlEscape($body) ?></textarea>
            </div>
            <div>
                <input
                    type="submit"
                    value="保存文章"
                />
                <a href="index.php">取消</a>
            </div>
        </form>
    </body>
</html>
