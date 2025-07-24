<?php 
// 获取项目的根目录路径
function getRootPath(){
    return realpath(__DIR__.'/..');
}

// 获取数据库文件的完整路径
function getDatabasePath(){
    return getROOtPath().'/data/data.sqlite';
}

// 获取用于PDO连接的DSN字符串
function getDsn(){
    return 'sqlite:' .getDatabasePath();
}

// 获取PDO数据库连接对象
function getPDO(){
    $pdo =new PDO(getDsn());

    // 开启外键约束，确保数据完整性
    $result=$pdo->query('PRAGMA foreign_keys = ON');
    if($result === false){
        throw new Exception('数据库无法开启外键约束');
    }

    return $pdo;
}

/**
 * 安全地转义HTML，防止XSS攻击
 *
 * @param string $html
 * @return string
 */
function htmlEscape($html)
{
    return htmlspecialchars($html, ENT_HTML5, 'UTF-8');
}

/**
 * 将SQL格式的日期（Y-m-d）转换为更友好的格式（d M Y）
 * @param string $sqlDate
 * @return string
 */
function convertSqlDate($sqlDate)
{
    $date = DateTime::createFromFormat('!Y-m-d', $sqlDate);

    if ($date === false) {
        return $sqlDate;
    }

    return $date->format('d M Y');
}

/**
 * 统计指定文章的评论总数
 * @param PDO $pdo 
 * @param integer $postId
 * @return integer
*/
function countCommentsForPost(PDO $pdo,$postId){
    $sql="
    SELECT
    COUNT(*) c
    FROM  comment
    WHERE post_id=:post_id
    ";
    $stmt=$pdo->prepare($sql);
    $stmt->execute(array('post_id'=>$postId));

    return (int) $stmt->fetchColumn();
}

/**
 * 获取指定文章的所有评论
 * @param PDO $pdo
 * @param integer $postId
 * @return array
 */
function getCommentsForPost(PDO $pdo,$postId){
    $sql = "
    SELECT 
    id, name, text, created_at, website
    id, title, created_at, body,
        (SELECT COUNT(*) FROM comment WHERE comment.post_id = post.id) comment_count
    FROM comment
    WHERE post_id = :post_id
    ";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(array('post_id' => $postId,));
    
    if (!$result) {
        throw new Exception('获取评论失败');
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 将换行符转换为HTML段落标签
function convertNewLinesToParagraphs($text){
    $escaped=htmlEscape($text);
    return '<p>'.str_replace("\n","<p></p>",$escaped).'</p>';
}

/**
 * 跳转到指定脚本并退出
 * @param string $script
 */
function redirectAndExit($script){
    $relativeUrl = $_SERVER['PHP_SELF'];
    $urlFolder = substr($relativeUrl, 0, strrpos($relativeUrl, '/') + 1);

    $host = $_SERVER['HTTP_HOST'];
    $fullUrl = 'http://' . $host . $urlFolder . $script;
    header('Location: ' . $fullUrl);
    exit();
}

// 获取当前时间的SQL格式字符串
function getSqlDateForNow()
{
    return date('Y-m-d H:i:s');
}

/**
 * 尝试登录，验证用户名和密码
 * @param PDO $pdo
 * @param string $username
 * @param string $password
 * @return boolean 成功返回true，失败返回false
 */
function tryLogin(PDO $pdo,$username,$password){
    $sql = "
    SELECT 
    password
    FROM
    user
    WHERE
    username =:username
    ";
    $stmt=$pdo->prepare($sql);
    $stmt->execute(array('username'=>$username,));

    $hash=$stmt->fetchColumn();
    $success=password_verify($password,$hash);
    return $success;
}

// 登录用户，设置session
function login($username){
    session_regenerate_id();
    $_SESSION['logged_in_username']=$username;
}

// 登出用户，销毁session
function logout(){
    unset($_SESSION['logged_in_username']);
}

// 获取当前登录的用户名
function getAuthUser(){
    return isLoggedIn()? $_SESSION['logged_in_username'] : null;
}

// 检查用户是否已登录
function isLoggedIn(){
    return isset($_SESSION['logged_in_username']);
}

// 根据当前登录的用户名，获取用户ID
function getAuthUserId(PDO $pdo){
    if(!isLoggedIn()){
        return null;
    }
    $sql='
    SELECT id
    FROM user
    WHERE username=:username
    ';
    $stmt=$pdo->prepare($sql);
    $stmt->execute(
        array(
            'username'=>getAuthUser()
        )
        );
        return $stmt->fetchColumn();
}



?>