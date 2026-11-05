<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        header("Location: avaliacoes.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $peso = $_POST['peso'];
    $altura = $_POST['altura'];
    $percentual_gordura = !empty($_POST['percentual_gordura']) ? $_POST['percentual_gordura'] : NULL;
    $percepcao_corporal = $_POST['percepcao_corporal'];
    $doencas_preexistentes = $_POST['doencas_preexistentes'];
    $limitacoes_fisicas = $_POST['limitacoes_fisicas'];

    $sql = "UPDATE avaliacoes SET peso = :peso, altura = :altura, percentual_gordura = :percentual_gordura, percepcao_corporal = :percepcao_corporal, doencas_preexistentes = :doencas_preexistentes, limitacoes_fisicas = :limitacoes_fisicas WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'peso' => $peso, 'altura' => $altura, 'percentual_gordura' => $percentual_gordura,
        'percepcao_corporal' => $percepcao_corporal, 'doencas_preexistentes' => $doencas_preexistentes,
        'limitacoes_fisicas' => $limitacoes_fisicas, 'id' => $id
    ]);

    header("Location: ver_avaliacao.php?id=" . $id);
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT av.*, al.nome FROM avaliacoes av JOIN alunos al ON av.aluno_id = al.id WHERE av.id = :id");
$stmt->execute(['id' => $id]);
$avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Avaliação - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #ffffff; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; }
        .nav-link { color: #aaaaaa; padding: 10px 15px; margin-bottom: 5px; border-radius: 8px; }
        .nav-link:hover, .nav-link.active { background-color: #f97316; color: #ffffff; }
        .form-control { background-color: #2a2a2a; border: 1px solid #444; color: white; }
        .form-control:focus { background-color: #333333; border-color: #f97316; color: white; box-shadow: none; }
        .form-label { color: #cccccc; font-size: 0.95em; font-weight: 500; }
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
                <li class="nav-item"><a href="exercicios.php" class="nav-link">Exercícios</a></li>
                <li class="nav-item"><a href="fichas.php" class="nav-link">Fichas de Treino</a></li>
                <li class="nav-item"><a href="avaliacoes.php" class="nav-link active">Avaliações Físicas</a></li>
            </ul>
        </div>
        
        <div class="content p-5 w-100">
            <h2 class="mb-4">Editar Avaliação: <span style="color:#f97316;"><?php echo $avaliacao['nome']; ?></span></h2>
            
            <div class="card p-4" style="background-color: #1e1e1e; border: none; border-radius: 10px;">
                <form action="editar_avaliacao.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $avaliacao['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Peso (kg)</label>
                            <input type="number" step="0.01" name="peso" class="form-control" value="<?php echo $avaliacao['peso']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Altura (m)</label>
                            <input type="number" step="0.01" name="altura" class="form-control" value="<?php echo $avaliacao['altura']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">% Gordura</label>
                            <input type="number" step="0.1" name="percentual_gordura" class="form-control" value="<?php echo $avaliacao['percentual_gordura']; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Como descreve o corpo?</label>
                            <input type="text" name="percepcao_corporal" class="form-control" value="<?php echo $avaliacao['percepcao_corporal']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Doenças Pré-existentes</label>
                            <textarea name="doencas_preexistentes" class="form-control" rows="3"><?php echo $avaliacao['doencas_preexistentes']; ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Limitações Físicas / Dores</label>
                            <textarea name="limitacoes_fisicas" class="form-control" rows="3"><?php echo $avaliacao['limitacoes_fisicas']; ?></textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-3">
                        <button type="submit" class="btn" style="background-color: #f97316; color: white; font-weight: bold;">Salvar Alterações</button>
                        <a href="ver_avaliacao.php?id=<?php echo $avaliacao['id']; ?>" class="btn btn-outline-light">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>