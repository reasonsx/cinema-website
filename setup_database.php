<?php
require('include/constants.php');

try {
    // Connect to MySQL server (no specific database)
    $pdo = new PDO("mysql:host=localhost;charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Read and execute SQL file
    $sql = file_get_contents('database.sql');
    $pdo->exec($sql);

    echo "✅ Database setup completed successfully.";
} catch (PDOException $e) {
    die("❌ DB Setup failed: " . $e->getMessage());
}
