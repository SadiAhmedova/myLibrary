<?php

try {
    $url = parse_url(getenv("DATABASE_URL"));

    $host = $url["host"];
    $user = $url["user"];
    $pass = $url["pass"];
    $db = ltrim($url["path"], "/");
    
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;user=$user;password=$pass";
    $pdo = new PDO($dsn);
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
} catch (Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
    exit;
}

?>