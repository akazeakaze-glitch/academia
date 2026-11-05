<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $especialidade = $_POST['especialidade'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $pdo->beginTransaction();

    try {

        $sql = "INSERT INTO professores (nome, especialidade, telefone) VALUES (:nome, :especialidade, :telefone)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nome' => $nome, 'especialidade' => $especialidade, 'telefone' => $telefone]);

        $sql_usuario = "INSERT INTO usuarios (nome, email, senha, perfil) VALUES (:nome, :email, :senha, 'professor')";
        $stmt_usuario = $pdo->prepare($sql_usuario);
        $stmt_usuario->execute(['nome' => $nome, 'email' => $email, 'senha' => $senha]);

        $pdo->commit();

        header("Location: professores.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao cadastrar: " . $e->getMessage() . " - Volte a página e tente novamente.");
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Novo Professor - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #ffffff; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; }
        .nav-link { color: #aaaaaa; padding: 10px 15px; margin-bottom: 5px; border-radius: 8px; }
        .nav-link:hover, .nav-link.active { background-color: #f97316; color: #ffffff; }
        .form-control { background-color: #2a2a2a; border: 1px solid #444; color: white; }
        .form-control:focus { background-color: #2a2a2a; border-color: #f97316; color: white; box-shadow: none; }
        /* Garante que o texto de exemplo (placeholder) fique legível */
        .form-control::placeholder { color: #aaaaaa; opacity: 1; }
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
            <h2 class="mb-4">Cadastrar Novo Professor</h2>
            <div class="card p-4" style="background-color: #1e1e1e; border: none; border-radius: 10px;">
                <form action="cadastrar_professor.php" method="POST">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: #ccc;">Nome Completo</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: #ccc;">Especialidade</label>
                            <input type="text" name="especialidade" class="form-control" placeholder="Ex: Musculação" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" style="color: #ccc;">Telefone (WhatsApp)</label>
                            <!-- Trava de 11 números e bloqueio de letras -->
                            <input type="text" name="telefone" class="form-control" maxlength="11" placeholder="Ex: 51999999999" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" style="color: #ccc;">E-mail de Acesso</label>
                            <input type="email" name="email" class="form-control" placeholder="email@corpoeforma.com" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" style="color: #ccc;">Senha de Acesso</label>
                            <input type="password" name="senha" class="form-control" placeholder="Crie uma senha" required>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn" style="background-color: #f97316; color: white; font-weight: bold;">Salvar Professor</button>
                        <a href="professores.php" class="btn btn-outline-light ms-2">Cancelar</a>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</body>
</html>