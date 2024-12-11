<?php

try {
    $url = parse_url(getenv("DATABASE_URL"));

    $host = 'd6ybckq58s9ru745.cbetxkdyhwsb.us-east-1.rds.amazonaws.com';
    $db   = 'jkby0y23ud5v4uld';
    $user = 'ar04n0qy5wvxa50h';
    $pass = 'y14wprujpq2tdm6i';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);

    $sql = file_get_contents('my_library.sql'); 

    if ($sql === false) {
        throw new Exception("Could not read the SQL file.");
    }

    $pdo->exec($sql);
    echo "SQL script executed successfully.";
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
} catch (Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
    exit;
}

?>