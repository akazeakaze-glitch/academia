<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: professores.php");
    exit;
}

$id_professor = $_GET['id'];

try {
    $stmt_busca = $pdo->prepare("SELECT nome FROM professores WHERE id = :id");
    $stmt_busca->execute(['id' => $id_professor]);
    $professor = $stmt_busca->fetch(PDO::FETCH_ASSOC);

    if ($professor) {
        $nome_prof = $professor['nome'];
        
        $pdo->beginTransaction();

        $sql_del_usu = "DELETE FROM usuarios WHERE nome = :nome AND perfil = 'professor'";
        $stmt_del_usu = $pdo->prepare($sql_del_usu);
        $stmt_del_usu->execute(['nome' => $nome_prof]);

        $sql_del_prof = "DELETE FROM professores WHERE id = :id";
        $stmt_del_prof = $pdo->prepare($sql_del_prof);
        $stmt_del_prof->execute(['id' => $id_professor]);

        $pdo->commit();
    }

    header("Location: professores.php");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Erro ao excluir o professor: " . $e->getMessage());
}
?>