<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: avaliacoes.php");
    exit;
}

$id = $_GET['id'];

$sql = "DELETE FROM avaliacoes WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);

header("Location: avaliacoes.php");
exit;
?>