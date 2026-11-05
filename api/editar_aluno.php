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
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $data_nascimento = $_POST['data_nascimento'];
    $objetivo = $_POST['objetivo'];

    $sql = "UPDATE alunos 
            SET nome = :nome, email = :email, telefone = :telefone, data_nascimento = :data_nascimento, objetivo = :objetivo 
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nome' => $nome,
        'email' => $email,
        'telefone' => $telefone,
        'data_nascimento' => $data_nascimento,
        'objetivo' => $objetivo,
        'id' => $id
    ]);

    header("Location: alunos.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: alunos.php");
    exit;
}

$id_aluno = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM alunos WHERE id = :id");
$stmt->execute(['id' => $id_aluno]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    header("Location: alunos.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Aluno - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #ffffff; overflow-x: hidden; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; box-shadow: 2px 0 10px rgba(0,0,0,0.5); }
        .nav-link { color: #aaaaaa; padding: 10px 15px; margin-bottom: 5px; border-radius: 8px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background-color: #f97316; color: #ffffff; font-weight: bold; }
        
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
                <li class="nav-item"><a href="#" class="nav-link">Professores</a></li>
                <li class="nav-item"><a href="exercicios.php" class="nav-link">Exercícios</a></li>
                <li class="nav-item"><a href="fichas.php" class="nav-link">Fichas de Treino</a></li>
                <li class="nav-item"><a href="avaliacoes.php" class="nav-link">Avaliações Físicas</a></li>
            </ul>
            <hr style="border-color: #444;">
            <a href="index.php" class="nav-link text-danger text-center">Sair do Sistema</a>
        </div>
        
        <div class="content p-5 w-100">
            <h2 class="mb-4">Editar Aluno</h2>
            
            <div class="card p-4" style="background-color: #1e1e1e; border: none; border-radius: 10px;">
                <form action="editar_aluno.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $aluno['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" name="nome" class="form-control" value="<?php echo $aluno['nome']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $aluno['email']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Telefone (WhatsApp)</label>
                            <input type="text" name="telefone" class="form-control" value="<?php echo $aluno['telefone']; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Data de Nascimento</label>
                            <input type="date" name="data_nascimento" class="form-control" value="<?php echo $aluno['data_nascimento']; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Objetivo Principal</label>
                            <select name="objetivo" class="form-select">
                                <option value="Hipertrofia" <?php if($aluno['objetivo'] == 'Hipertrofia') echo 'selected'; ?>>Hipertrofia</option>
                                <option value="Emagrecimento" <?php if($aluno['objetivo'] == 'Emagrecimento') echo 'selected'; ?>>Emagrecimento</option>
                                <option value="Condicionamento" <?php if($aluno['objetivo'] == 'Condicionamento') echo 'selected'; ?>>Condicionamento</option>
                                <option value="Reabilitação" <?php if($aluno['objetivo'] == 'Reabilitação') echo 'selected'; ?>>Reabilitação</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn" style="background-color: #f97316; color: white; font-weight: bold;">Salvar Alterações</button>
                        <a href="alunos.php" class="btn btn-outline-light">Cancelar</a>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>

</body>
</html>