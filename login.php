<?php
require_once 'lib/common.php';

session_start();

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
    <title>A log application | login</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
</head>
<body>
    <?php require 'templates/title.php' ?>
    <?php if ($username): ?>
        <div style="border:1px solid #ff6666;padding:6px;">
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
        <input type="submit" name="submit" value="login"/>
    </form>
</body>
</html>
