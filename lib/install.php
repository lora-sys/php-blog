<?php 

function installBlog(PDO $pdo) 
{
    // 获取项目根目录
    $root = getRootPath();
    $database = getDatabasePath();
    $error = '';

    // 安全措施，如果数据库文件已存在，则阻止安装
    if (is_readable($database) && filesize($database) > 0)
    {
        $error = '请在重新安装前，手动删除已存在的数据库文件';
    }

    // 如果没有错误，则创建空的数据库文件
    if (!$error)
    {
        $createdOk = @touch($database);
        if (!$createdOk)
        {
            $error = sprintf(
                '无法创建数据库，请检查服务器在 %s 目录下的文件写入权限',
                dirname($database)
            );
        }
    }

    // 获取用于初始化的SQL命令
    if (!$error)
    {
        $sql = file_get_contents($root . '/data/init.sql');
        if ($sql === false)
        {
            $error = '找不到SQL初始化文件';
        }
    }

    // 执行SQL命令
    if (!$error)
    {
        $statements = explode(';', $sql);
        foreach ($statements as $statement)
        {
            $statement = trim($statement);
            if ($statement)
            {
                $result = $pdo->exec($statement);
                if ($result === false)
                {
                    $error = '无法执行SQL: ' . print_r($pdo->errorInfo(), true);
                    break;
                }
            }
        }
    }

    // 统计每个表中创建的行数
    $count = array();
    foreach (array('post', 'comment', 'user') as $tableName)
    {
        if (!$error)
        {
            $sql = "SELECT COUNT(*) AS c FROM " . $tableName;
            $stmt = $pdo->query($sql);
            if ($stmt)
            {
                $count[$tableName] = $stmt->fetchColumn();
            }
        }
    }
    return array($count, $error);
}

// 在数据库中为管理员创建初始密码
function createUser(PDO $pdo, $username, $length = 10)
{
    // 生成一个随机密码
    $alphabet = range(ord('A'), ord('Z'));
    $alphabetSize = count($alphabet);

    $password = '';
    for ($i = 0; $i < $length; $i++)
    {
        $letterCode = $alphabet[rand(0, $alphabetSize - 1)];
        $password .= chr($letterCode);
    }

    $error = '';
    // 更新用户的SQL语句
    $sql = '
        UPDATE 
        user
        SET
            password=:password,
            created_at=:created_at,
            is_enabled = 1
        WHERE
            username=:username
    ';
    $stmt = $pdo->prepare($sql);
    if ($stmt === false)
    {
        $error = '无法准备用户更新语句';
    }

    // 对密码进行哈希处理
    if (!$error)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($hash === false)
        {
            $error = '无法对密码进行哈希处理';
        }
    }

    // 执行更新语句
    if (!$error)
    {
        $result = $stmt->execute(
            array(
                'username' => $username,
                'password' => $hash,
                'created_at' => getSqlDateForNow(),
            )
        );
        if ($result === false)
        {
            $error = "无法执行用户密码更新语句";
        }
    }
    
    return array($password, $error);
}