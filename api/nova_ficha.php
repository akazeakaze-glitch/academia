<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aluno_id = $_POST['aluno_id'];
    $nome_ficha = $_POST['nome_ficha'];
    $data_criacao = date('Y-m-d');
    $data_vencimento = $_POST['data_vencimento'];

    $sql = "INSERT INTO fichas (aluno_id, nome_ficha, data_criacao, data_vencimento) 
            VALUES (:aluno_id, :nome_ficha, :data_criacao, :data_vencimento)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'aluno_id' => $aluno_id,
        'nome_ficha' => $nome_ficha,
        'data_criacao' => $data_criacao,
        'data_vencimento' => $data_vencimento
    ]);

    $nova_ficha_id = $pdo->lastInsertId();
    header("Location: ver_treino.php?id=" . $nova_ficha_id);
    exit;
}

$stmt_alunos = $pdo->query("SELECT id, nome FROM alunos ORDER BY nome ASC");
$alunos = $stmt_alunos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nova Ficha - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #ffffff; overflow-x: hidden; }
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
                <li class="nav-item"><a href="exercicios.php" class="nav-link">Exercícios</a></li>
                <li class="nav-item"><a href="fichas.php" class="nav-link active">Fichas de Treino</a></li>
            </ul>
        </div>
        
        <div class="content p-5 w-100">
            <h2 class="mb-4">Criar Nova Ficha de Treino</h2>
            
            <div class="card p-4" style="background-color: #1e1e1e; border: none; border-radius: 10px;">
                <form action="nova_ficha.php" method="POST">
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Selecione o Aluno</label>
                            <select name="aluno_id" class="form-select" required>
                                <option value="">Escolha um aluno...</option>
                                <?php foreach ($alunos as $aluno): ?>
                                    <option value="<?php echo $aluno['id']; ?>"><?php echo $aluno['nome']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Nome da Ficha (Ex: Adaptação, Hipertrofia...)</label>
                            <input type="text" name="nome_ficha" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Válida até</label>
                            <input type="date" name="data_vencimento" class="form-control" required>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn" style="background-color: #f97316; color: white; font-weight: bold;">Criar Ficha e Adicionar Exercícios</button>
                        <a href="fichas.php" class="btn btn-outline-light">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>