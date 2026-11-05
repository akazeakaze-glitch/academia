<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $sexo = $_POST['sexo'];
    $telefone = $_POST['telefone'];
    $data_nascimento = $_POST['data_nascimento'];
    $objetivo = $_POST['objetivo'];
    
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO alunos (nome, email, senha, sexo, telefone, data_nascimento, objetivo) 
            VALUES (:nome, :email, :senha, :sexo, :telefone, :data_nascimento, :objetivo)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nome' => $nome, 
        'email' => $email, 
        'senha' => $senha, 
        'sexo' => $sexo, 
        'telefone' => $telefone, 
        'data_nascimento' => $data_nascimento, 
        'objetivo' => $objetivo
    ]);
    
    $novo_aluno_id = $pdo->lastInsertId();

    $nome_ficha = "Treino Padrão Inicial";
    $data_criacao = date('Y-m-d');
    $data_vencimento = date('Y-m-d', strtotime('+45 days')); 
    
    $sql_ficha = "INSERT INTO fichas (aluno_id, nome_ficha, data_criacao, data_vencimento) 
                  VALUES (:aluno_id, :nome_ficha, :data_criacao, :data_vencimento)";
    $stmt_ficha = $pdo->prepare($sql_ficha);
    $stmt_ficha->execute(['aluno_id' => $novo_aluno_id, 'nome_ficha' => $nome_ficha, 'data_criacao' => $data_criacao, 'data_vencimento' => $data_vencimento]);
    
    $nova_ficha_id = $pdo->lastInsertId();

    $template_aluno_id = ($sexo == 'M') ? 998 : 999;
    
    $stmt_get_template = $pdo->prepare("SELECT id FROM fichas WHERE aluno_id = :template_id LIMIT 1");
    $stmt_get_template->execute(['template_id' => $template_aluno_id]);
    $ficha_template = $stmt_get_template->fetch(PDO::FETCH_ASSOC);

    if ($ficha_template) {
        $template_ficha_id = $ficha_template['id'];
        
        $sql_clone = "INSERT INTO ficha_exercicios (ficha_id, exercicio_id, divisao, series, repeticoes, carga)
                      SELECT :nova_ficha_id, exercicio_id, divisao, series, repeticoes, carga 
                      FROM ficha_exercicios 
                      WHERE ficha_id = :template_ficha_id";
        $stmt_clone = $pdo->prepare($sql_clone);
        $stmt_clone->execute([
            'nova_ficha_id' => $nova_ficha_id,
            'template_ficha_id' => $template_ficha_id
        ]);
    }

    header("Location: alunos.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Novo Aluno - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #ffffff; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; box-shadow: 2px 0 10px rgba(0,0,0,0.5); }
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
                <li class="nav-item"><a href="alunos.php" class="nav-link active">Alunos</a></li>
                <li class="nav-item"><a href="professores.php" class="nav-link">Professores</a></li>
                <li class="nav-item"><a href="exercicios.php" class="nav-link">Exercícios</a></li>
                <li class="nav-item"><a href="fichas.php" class="nav-link">Fichas de Treino</a></li>
                <li class="nav-item"><a href="avaliacoes.php" class="nav-link">Avaliações Físicas</a></li>
            </ul>
        </div>
        
        <div class="content p-5 w-100">
            <h2 class="mb-4">Cadastrar Novo Aluno</h2>
            
            <div class="card p-4" style="background-color: #1e1e1e; border: none; border-radius: 10px;">
                <form action="cadastrar_aluno.php" method="POST">
                    
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Sexo</label>
                            <select name="sexo" class="form-select" required>
                                <option value="M">Masculino</option>
                                <option value="F">Feminino</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Telefone (WhatsApp)</label>
                            <input type="text" name="telefone" class="form-control" maxlength="11" placeholder="Ex: 51999999999" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Data de Nascimento</label>
                            <input type="date" name="data_nascimento" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Objetivo Principal</label>
                            <select name="objetivo" class="form-select">
                                <option value="Hipertrofia">Hipertrofia</option>
                                <option value="Emagrecimento">Emagrecimento</option>
                                <option value="Condicionamento">Condicionamento</option>
                                <option value="Reabilitação">Reabilitação</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-light">Senha de Acesso</label>
                            <input type="password" name="senha" class="form-control" required placeholder="Crie uma senha para o aluno">
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn" style="background-color: #f97316; color: white; font-weight: bold;">Salvar Aluno e Gerar Treino</button>
                        <a href="alunos.php" class="btn btn-outline-light">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>