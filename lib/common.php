<?php 
// @return string
// workout the path to the database so sqlite/pdp can connect
function getROOtPath(){
    return realpath(__DIR__.'/..');
}
// get full path for the database file
//@return string
function getDatabasePath(){
    return getROOtPath().'/data/data.sqlite';
}
// get the dsn for the sqite common

function getDsn(){
    return 'sqlite:' .getDatabasePath();
}
//get the pdo object for the database
//@return PDO
function getPDO(){
$pdo =new PDO(getDsn());

//外键约束 常量，必须在sqlite
$result=$pdo->query('PRAGMA foreign_keys = ON');
if($result === false){
    throw new Exception('There was a problem enabling foreign keys');
}

return $pdo;

}
//escapes html so it is safe to output
//@param string $html
//@return string
/**
 * Escapes HTML so it is safe to output
 *
 * @param string $html
 * @return string
 */
function htmlEscape($html)
{
    return htmlspecialchars($html, ENT_HTML5, 'UTF-8');
}

function convertSqlDate($sqlDate)
{
    // SQLite's date() function provides the 'Y-m-d' format.
    // We use '!' to ensure the time is set to 00:00:00 and avoid any ambiguity.
    $date = DateTime::createFromFormat('!Y-m-d', $sqlDate);

    // If parsing fails for any reason, just return the original string to avoid crashing.
    if ($date === false) {
        return $sqlDate;
    }

    // If successful, format it into a user-friendly date.
    return $date->format('d M Y');
}

/**
 * returns number of the comments for the specified post
* @param PDO $pdo 
* @param integer $postid $name
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
 * return all the comments for the specified post
 * @param integer $postId  $pdo
 * @return array
 */
function getCommentsForPost(PDO $pdo,$postId){
    $pdo = getPDO();
    $sql = "
    SELECT 
    id, name, text, created_at, website
    FROM comment
    WHERE post_id = :post_id
    ";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(array('post_id' => $postId,));
    
    if (!$result) {
        throw new Exception('Failed to execute query for comments.');
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// 安全转换文本，段落，html。接受文本，返回字符串文本
function convertNewLinesToParagraphs($text){
    $escaped=htmlEscape($text);
    return '<p>'.str_replace("\n","<p></p>",$escaped).'</p>';
}


function redirectAndExit($script){
    $relativeUrl = $_SERVER['PHP_SELF'];
    $urlFolder = substr($relativeUrl, 0, strrpos($relativeUrl, '/') + 1);
    //#urlFolder=dirname( $relativeUrl);)
    // Redirect to the full URL (http://myhost/blog/script.php)
    $host = $_SERVER['HTTP_HOST'];
    $fullUrl = 'http://' . $host . $urlFolder . $script;
    header('Location: ' . $fullUrl);
    exit();
}


function getSqlDateForNow()
{
    return date('Y-m-d H:i:s');
}

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
    //执行查询语句，从用户表获取密码，并使用密码验证库检查跟数据库密码一样不一样
    $hash=$stmt->fetchColumn();
    $success=password_verify($password,$hash);
    return $success;
    // 返回成功或者失败，与数据库对应的密码比对
}
function login($username){
    session_regenerate_id();
    $_SESSION['logged_in_username']=$username;
}
//用户登出
function logout(){
    unset($_SESSION['logged_in_username']);
}
function getAuthUser(){
    return isLoggedIn()? $_SESSION['logged_in_username'] : null;
}

function isLoggedIn(){
    return isset($_SESSION['logged_in_username']);
}


function getAuthUserId(PDO $pdo){
    //没有登录返回空
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