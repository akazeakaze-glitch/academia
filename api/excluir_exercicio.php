<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: exercicios.php");
    exit;
}

$sql = "DELETE FROM exercicios WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $_GET['id']]);

header("Location: exercicios.php");
exit;
?>