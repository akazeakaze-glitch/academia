<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: alunos.php");
    exit;
}

$id_do_aluno = $_GET['id'];

$sql = "DELETE FROM alunos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id_do_aluno]);

header("Location: alunos.php");
exit;
?> 