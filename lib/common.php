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
return new PDO(getDsn());
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

function convertSqlDate($sqlDate){
    /*@var $date DateTime */
    $date=DateTime::createFromFormat('Y-m-d H:i:s',$sqlDate);
    return $date->format('d M Y,H:i');
}
/**
 * returns number of the comments for the specified post
 * @param integer $postid $name
 * @return integer
*/
function countCommentsForPost($postId){
$pdo=getPDO();
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
 * @param integer $postId 
 */
function getCommentsForPost($postId){
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
?>