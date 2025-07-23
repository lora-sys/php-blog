<?php 

function installBlog(PDO $pdo) 
{
    //获取一个游泳池的项目路径
    $root = getRootPath();
    $database = getDatabasePath();
    $error = '';

    // A security measure, to avoid anyone resetting the database if it already exists
    if (is_readable($database) && filesize($database) > 0)
    {
        $error = 'Please delete the existing database manually before installing it afresh';
    }

    // Create an empty file for the database
    if (!$error)
    {
        $createdOk = @touch($database);
        if (!$createdOk)
        {
            $error = sprintf(
                'Could not create the database, please allow the server to create new files in %s',
                dirname($database)
            );
        }
    }

    // Grab the SQL commands we want to run on the database
    if (!$error)
    {
        $sql = file_get_contents($root . '/data/init.sql');
        if ($sql === false)
        {
            $error = 'Cannot find SQL file';
        }
    }

    // Connect to the new database and try to run the SQL commands
    if (!$error)
    {
        $result = $pdo->exec($sql);
        if ($result === false)
        {
            $error = 'Could not run SQL: ' . print_r($pdo->errorInfo(), true);
        }
    }

    // See how many rows we created, if any
    $count = array();
    foreach (array('post', 'comment') as $tableName)
    {
        if (!$error)
        {
            $sql = "SELECT COUNT(*) AS c FROM " . $tableName;
            $stmt = $pdo->query($sql);
            if ($stmt)
            {
                // We store each count in an array
                $count[$tableName] = $stmt->fetchColumn();
            }
        }
    }
    return array($count, $error);
}

// 博客创建新用户在数据库 ，函数接受用户名
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
    //数据库插入语句
    $sql = '
        INSERT INTO user
            (username, password, created_at)
        VALUES
            (:username, :password, :created_at)
    ';
    $stmt = $pdo->prepare($sql);
    if ($stmt === false)
    {
        $error = '不能勾创建用户';
    }

    //目前密码用明文存储，没有用加密，会导致问题
    if (!$error)
    {
        //创造一个哈希的密码。去保存数据库密码
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($hash === false)
        {
            $error = '不能创建密码哈希';
        }
    }

    //执行插入语句
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
            $error = "不能运行创建用户进入的语句";
        }
    }
    // if (!$error ) $password=""; //在创造后，防止明文密码。会在存入数据库后。清空？
    return array($password, $error);
}


// 博客安装程序，返回数组，个数，和错误信息