<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $grupo_muscular = $_POST['grupo_muscular'];
    $equipamento = $_POST['equipamento'];
    $instrucoes = $_POST['instrucoes'];

    $sql = "INSERT INTO exercicios (nome, grupo_muscular, equipamento, instrucoes) 
            VALUES (:nome, :grupo_muscular, :equipamento, :instrucoes)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nome' => $nome, 
        'grupo_muscular' => $grupo_muscular, 
        'equipamento' => $equipamento,
        'instrucoes' => $instrucoes
    ]);

    header("Location: exercicios.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Novo Exercício - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #ffffff; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; }
        .nav-link { color: #aaaaaa; padding: 10px 15px; margin-bottom: 5px; border-radius: 8px; }
        .nav-link:hover, .nav-link.active { background-color: #f97316; color: #ffffff; }
        .form-control, .form-select { background-color: #2a2a2a; border: 1px solid #444; color: white; }
        .form-control:focus, .form-select:focus { background-color: #2a2a2a; border-color: #f97316; color: white; box-shadow: none; }
        .form-label { color: #ccc; }
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
                <li class="nav-item"><a href="professores.php" class="nav-link">Professores</a></li>
                <li class="nav-item"><a href="exercicios.php" class="nav-link active">Exercícios</a></li>
                <li class="nav-item"><a href="fichas.php" class="nav-link">Fichas de Treino</a></li>
                <li class="nav-item"><a href="avaliacoes.php" class="nav-link">Avaliações Físicas</a></li>
            </ul>
        </div>
        
        <div class="content p-5 w-100">
            <h2 class="mb-4">Cadastrar Novo Exercício</h2>
            <div class="card p-4" style="background-color: #1e1e1e; border: none; border-radius: 10px;">
                <form action="cadastrar_exercicio.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome do Exercício</label>
                            <input type="text" name="nome" class="form-control" placeholder="Ex: Leg Press 45º" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Grupo Muscular</label>
                            <select name="grupo_muscular" class="form-select" required>
                                <option value="Peito">Peito</option>
                                <option value="Costas">Costas</option>
                                <option value="Pernas">Pernas</option>
                                <option value="Ombros">Ombros</option>
                                <option value="Braços">Braços</option>
                                <option value="Abdômen">Abdômen</option>
                                <option value="Cardio">Cardio</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Equipamento Utilizado</label>
                            <input type="text" name="equipamento" class="form-control" placeholder="Ex: Máquina, Halteres, Peso Corporal..." required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Instruções de Execução (Opcional)</label>
                            <textarea name="instrucoes" class="form-control" rows="3" placeholder="Dicas para realizar o movimento corretamente..."></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn" style="background-color: #f97316; color: white; font-weight: bold;">Salvar Exercício</button>
                        <a href="exercicios.php" class="btn btn-outline-light ms-2">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>