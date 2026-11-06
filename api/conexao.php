<?php
$host = 'mysql-academia.alwaysdata.net';
$port = '3306';
$dbname = 'academia_corpoeforma';
$user = 'academia';
$password = 'Festa3199';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
