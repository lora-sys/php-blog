<?php
require_once 'lib/common.php';

session_start();
if(isLoggedIn()){
    redirectAndExit('index.php');
}
$username = '';
if ($_POST) {
    $pdo = getPDO();
    $username = $_POST['username'];
    $password = $_POST['password']; // This line was missing
    $ok = tryLogin($pdo, $username, $password);
    if ($ok) {
        login($username);
        redirectAndExit('index.php');
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>博客应用 | 登录</title>
    <?php require 'templates/head.php'?>
</head>
<body>
    <?php require 'templates/title.php' ?>
    <?php if ($username): ?>
        <div class="error box">
            这个用户名或者密码不正确，再试一次
        </div>
    <?php endif; ?>
    <p>登录:</p>
    <form method="post">
        <p>用户名:
            <input
                type="text"
                name="username"
                value="<?php echo htmlEscape($username) ?>"
            />
        </p>
        <p>
            密码:
            <input
                type="password"
                name="password"
            />
        </p>
        <input type="submit" name="submit" value="登录"/>
    </form>
</body>
</html>
