<?php
$host = 'mysql-d9ad40f-corpoeforma.h.aivencloud.com';
$port = '24506';
$dbname = 'defaultdb';
$user = 'avnadmin';
$password = 'AVNS_UhJh7UVxiBLy-kWJEc_';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
