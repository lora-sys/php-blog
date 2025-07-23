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
   $_SESSION['password']=$password;
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
        <title>Blog installer</title>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <style type="text/css">
            .box {
                border: 1px dotted silver;
                border-radius: 5px;
                padding: 4px;
            }
            .error {
                background-color: #ff6666;
            }
            .success {
                background-color: #88ff88;
            }
        </style>
    </head>
    <body>
        <?php if ($attempted): ?>

            <?php if ($error): ?>
                <div class="error box">
                    <?php echo htmlspecialchars($error) ?>
                </div>
            <?php else: ?>
                <div class="success box">
                    The database and demo data were created OK.
                    <?php // REPORT the counts for each table?>
                    <?php foreach (array('post', 'comment') as $tableName): ?>
                        <?php if (isset($count[$tableName])): ?>
                            <?php echo $count[$tableName] ?> new
                            <?php echo $tableName ?>s
                            were created.
                        <?php endif ?>
                    <?php endforeach ?>
                    <?php //报告新密码?>
                    The new '<?php echo htmlEscape($username) ?>'password is 
                    <span style="font-size:1.2em"><?php echo htmlEscape($password) ?></span>
                    
                </div>
            <?php endif ?>
        <p>
            <a href="index.php">View the blog</a> 
            <a href="install.php"> install again</a> 
            </p>
        <?php else: ?>

            <p>Click the install button to reset the database.</p>

            <form method="post" action="install.php">
                <input
                    name="install"
                    type="submit"
                    value="Install"
                    />
            </form>

        <?php endif ?>
    </body>
</html>
