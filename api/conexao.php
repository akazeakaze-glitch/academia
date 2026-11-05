<?php
$host = 'mysql-d9ad40f-corpoeforma.h.aivencloud.com';
$port = '24506';
$dbname = 'defaultdb';
$usuario = 'avnadmin';
$senha = 'AVNS_UhJh7UVxiBLy-kWJEc_';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
    exit;
}
?>