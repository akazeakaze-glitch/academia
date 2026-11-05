<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $especialidade = $_POST['especialidade'];
    $telefone = $_POST['telefone'];
    
    $sql = "UPDATE professores SET nome = :nome, especialidade = :especialidade, telefone = :telefone WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nome' => $nome, 'especialidade' => $especialidade, 'telefone' => $telefone, 'id' => $id]);

    header("Location: professores.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: professores.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM professores WHERE id = :id");
$stmt->execute(['id' => $_GET['id']]);
$prof = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Professor - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #ffffff; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; }
        .nav-link { color: #aaaaaa; padding: 10px 15px; margin-bottom: 5px; border-radius: 8px; }
        .nav-link:hover, .nav-link.active { background-color: #f97316; color: #ffffff; }
        .form-control { background-color: #2a2a2a; border: 1px solid #444; color: white; }
        .form-control:focus { background-color: #2a2a2a; border-color: #f97316; color: white; box-shadow: none; }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="sidebar p-3 d-flex flex-column">
            <h4 class="text-center mt-3 mb-4" style="color: #f97316;">Corpo e Forma</h4>
            <hr style="border-color: #444;">
            <ul class="nav flex-column mb-auto">
                <li class="nav-item"><a href="painel.php" class="nav-link">Início</a></li>
                <li class="nav-item"><a href="alunos.php" class="nav-link">Alunos</a></li>
                <li class="nav-item"><a href="professores.php" class="nav-link active">Professores</a></li>
                <li class="nav-item"><a href="exercicios.php" class="nav-link">Exercícios</a></li>
                <li class="nav-item"><a href="fichas.php" class="nav-link">Fichas de Treino</a></li>
                <li class="nav-item"><a href="avaliacoes.php" class="nav-link">Avaliações Físicas</a></li>
            </ul>
        </div>
        
        <div class="content p-5 w-100">
            <h2 class="mb-4">Editar Professor</h2>
            <div class="card p-4" style="background-color: #1e1e1e; border: none; border-radius: 10px;">
                <form action="editar_professor.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $prof['id']; ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: #ccc;">Nome Completo</label>
                            <input type="text" name="nome" class="form-control" value="<?php echo $prof['nome']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: #ccc;">Especialidade</label>
                            <input type="text" name="especialidade" class="form-control" value="<?php echo $prof['especialidade']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" style="color: #ccc;">Telefone (WhatsApp)</label>
                            <input type="text" name="telefone" class="form-control" value="<?php echo $prof['telefone']; ?>" required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn" style="background-color: #f97316; color: white; font-weight: bold;">Salvar Alterações</button>
                        <a href="professores.php" class="btn btn-outline-light ms-2">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>