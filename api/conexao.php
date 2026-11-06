<?php
$host = trim('mysql-d9ad40f-corpoeforma.h.aivencloud.com');
$port = '24506';
$dbname = 'defaultdb';
$user = 'avnadmin';
$password = trim('AVNS_UhJh7UVxiBLy-kWJEc_');

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_SSL_CA => true,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
    ];

    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $password, $options);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
