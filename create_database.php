<?php
include('db.php');

$sql = file_get_contents('create_database.sql'); 

try {
    $pdo->exec($sql);
    echo "Database and tables created successfully!";
} catch (PDOException $e) {
    echo "Error executing SQL: " . $e->getMessage();
}
?>
