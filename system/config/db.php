<?php
$host = getenv('DB_HOST') ?: "localhost";
$db   = getenv('DB_NAME') ?: "frisucode_ms";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') ?: "";
$port = getenv('DB_PORT') ?: "3306";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_SSL_CA       => true,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}