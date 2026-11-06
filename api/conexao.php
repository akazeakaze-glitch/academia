<?php
$host = 'mysql-d9ad40f-corpoeforma.h.aivencloud.com';
$port = '24506';
$dbname = 'defaultdb';
$user = 'avnadmin';
$password = 'COPIE_E_COLE_AQUI_A_SUA_SENHA_DA_AIVEN';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
