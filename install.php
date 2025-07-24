<?php error_reporting(E_ALL);ini_set('display_errors', 1);
?>
<?php
require 'lib/common.php';

require 'lib/install.php';

// We store stuff in the session, to survive the redirect
session_start();

// Let's run the installer when we receive a POST request
if ($_POST)
{
    // Here we store the results of the installation in the session
    $pdo =getPDO();
    list($rowCounts,$error)=installBlog($pdo);
    $password='';
    if(!$error){
        $username='admin';
        list($password,$error)=createUser($pdo,$username);
    }
   $_SESSION['count']=$rowCounts;
   $_SESSION['password']=$password;
   $_SESSION['username']=$username;
    $_SESSION['error']=$error;
    $_SESSION['try-install']=true;
    // Redirect to self, so we can show the results (from POST to GET)
    redirectAndExit('install.php');

}    

// Let's report on the installation attempt
$attempted = isset($_SESSION['error']) || isset($_SESSION['count']);
if (isset($_SESSION['try-install']))
{
    $error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
    $count = isset($_SESSION['count']) ? $_SESSION['count'] : array();
    $password =$_SESSION['password'];
    $username =$_SESSION['username'];
    // Unset the session data, so we only report the install/failure once
    unset($_SESSION['count'], $_SESSION['error']);
    unset($_SESSION['try-install'],$_SESSION['password'],$_SESSION['username']);
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>博客安装程序</title>
        <?php require 'templates/head.php'?>
    </head>
    <body>
        <?php if ($attempted): ?>

            <?php if ($error): ?>
                <div class="error box">
                    <?php echo htmlspecialchars($error) ?>
                </div>
            <?php else: ?>
                <div class="success box">
                    数据库和演示数据已成功创建。
                    <?php // REPORT the counts for each table?>
                    <?php foreach (array('post', 'comment') as $tableName): ?>
                        <?php if (isset($count[$tableName])): ?>
                            创建了 <?php echo $count[$tableName] ?> 条新的
                            <?php echo $tableName ?> 记录。
                        <?php endif ?>
                    <?php endforeach ?>
                    <?php //报告新密码?>
                    新的 '<?php echo htmlEscape($username) ?>' 用户密码是：
                   <span class="install-password"><?php echo htmlEscape($password)?></span>
                    
                </div>
            <?php endif ?>
        <p>
            <a href="index.php">查看博客</a> 
            <a href="install.php"> 再次安装</a> 
            </p>
        <?php else: ?>

            <p>点击下方的安装按钮来重置数据库。</p>

            <form method="post" action="install.php">
                <input
                    name="install"
                    type="submit"
                    value="安装"
                    />
            </form>

        <?php endif ?>
    </body>
</html>
