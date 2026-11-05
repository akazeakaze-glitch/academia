<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: fichas.php");
    exit;
}

$id_da_ficha = $_GET['id'];

$sql = "DELETE FROM fichas WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id_da_ficha]);

header("Location: fichas.php");
exit;
?>