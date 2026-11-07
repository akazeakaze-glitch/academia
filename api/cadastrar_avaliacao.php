<?php
session_start();
require_once __DIR__ . '/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$mensagem_sucesso = '';
$mensagem_erro = '';

$stmt_alunos = $pdo->query("SELECT id, nome FROM alunos ORDER BY nome ASC");
$alunos = $stmt_alunos->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aluno_id = $_POST['aluno_id'] ?? null;
    $data_avaliacao = $_POST['data_avaliacao'] ?? date('Y-m-d');
    $peso = $_POST['peso'] ?? null;
    $altura = $_POST['altura'] ?? null;
    $cintura = $_POST['cintura'] ?? null;
    $abdomen = $_POST['abdomen'] ?? null;
    $quadril = $_POST['quadril'] ?? null;
    $torax = $_POST['torax'] ?? null;
    $braco_direito = $_POST['braco_direito'] ?? null;
    $braco_esquerdo = $_POST['braco_esquerdo'] ?? null;
    $coxa_direita = $_POST['coxa_direita'] ?? null;
    $coxa_esquerda = $_POST['coxa_esquerda'] ?? null;

    if ($aluno_id && $peso) {
        try {
            $sql = "INSERT INTO avaliacoes (aluno_id, data_avaliacao, peso, altura, cintura, abdomen, quadril, torax, braco_direito, braco_esquerdo, coxa_direita, coxa_esquerda) 
                    VALUES (:aluno_id, :data_avaliacao, :peso, :altura, :cintura, :abdomen, :quadril, :torax, :braco_direito, :braco_esquerdo, :coxa_direita, :coxa_esquerda)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'aluno_id' => $aluno_id,
                'data_avaliacao' => $data_avaliacao,
                'peso' => $peso,
                'altura' => $altura,
                'cintura' => $cintura,
                'abdomen' => $abdomen,
                'quadril' => $quadril,
                'torax' => $torax,
                'braco_direito' => $braco_direito,
                'braco_esquerdo' => $braco_esquerdo,
                'coxa_direita' => $coxa_direita,
                'coxa_esquerda' => $coxa_esquerda
            ]);

            $mensagem_sucesso = "Avaliação física cadastrada com sucesso!";
        } catch (PDOException $e) {
            $mensagem_erro = "Erro ao cadastrar avaliação: " . $e->getMessage();
        }
    } else {
        $mensagem_erro = "Por favor, selecione o aluno e informe o peso.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Avaliação - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body { background-color: #121212; color: #ffffff; font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; box-shadow: 2px 0 10px rgba(0,0,0,0.5); }
        .nav-link { color: #aaaaaa; margin-bottom: 5px; border-radius: 8px; padding: 10px 15px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background-color: #f97316; color: #ffffff; font-weight: bold; }
        .card-dark { background-color: #1e1e1e; border: 1px solid #333; border-radius: 12px; }
        .form-control, .form-select { background-color: #121212; color: #ffffff; border: 1px solid #444; border-radius: 8px; }
        .form-control:focus, .form-select:focus { background-color: #181818; color: #ffffff; border-color: #f97316; box-shadow: none; }
        .form-label { color: #ccc; font-weight: 500; font-size: 0.9rem; }
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
                <li class="nav-item"><a href="ver_feedbacks.php" class="nav-link">Feedbacks</a></li>
            </ul>
            
            <hr style="border-color: #444;">
            <a href="index.php" class="nav-link text-danger text-center">Sair do Sistema</a>
        </div>
        
        <!-- CONTEÚDO PRINCIPAL -->
        <div class="content p-5 w-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Cadastrar Avaliação Física</h2>
                <a href="avaliacoes.php" class="btn text-white" style="background-color: #252525; border: 1px solid #444;">
                    Voltar
                </a>
            </div>

            <?php if ($mensagem_sucesso): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($mensagem_sucesso) ?>
                </div>
            <?php endif; ?>

            <?php if ($mensagem_erro): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($mensagem_erro) ?>
                </div>
            <?php endif; ?>

            <div class="card card-dark p-4">
                <form method="POST" action="">
                    
                    <h5 class="mb-3" style="color: #f97316; border-bottom: 1px solid #333; padding-bottom: 8px;">Dados Gerais</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="aluno_id" class="form-label">Aluno *</label>
                            <select class="form-select" id="aluno_id" name="aluno_id" required>
                                <option value="">Selecione um aluno...</option>
                                <?php foreach ($alunos as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="data_avaliacao" class="form-label">Data da Avaliação *</label>
                            <input type="date" class="form-control" id="data_avaliacao" name="data_avaliacao" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="peso" class="form-label">Peso (kg) *</label>
                            <input type="number" step="0.1" class="form-control" id="peso" name="peso" placeholder="Ex: 75.5" required>
                        </div>
                        <div class="col-md-6">
                            <label for="altura" class="form-label">Altura (m)</label>
                            <input type="number" step="0.01" class="form-control" id="altura" name="altura" placeholder="Ex: 1.75">
                        </div>
                    </div>

                    <!-- PERIMETRIA / MEDIDAS CORPORAIS -->
                    <h5 class="mb-3" style="color: #f97316; border-bottom: 1px solid #333; padding-bottom: 8px;">Perimetria / Medidas Corporais (cm)</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="cintura" class="form-label">Cintura</label>
                            <input type="number" step="0.1" class="form-control" id="cintura" name="cintura" placeholder="Ex: 80.0">
                        </div>
                        <div class="col-md-3">
                            <label for="abdomen" class="form-label">Abdômen</label>
                            <input type="number" step="0.1" class="form-control" id="abdomen" name="abdomen" placeholder="Ex: 84.5">
                        </div>
                        <div class="col-md-3">
                            <label for="quadril" class="form-label">Quadril</label>
                            <input type="number" step="0.1" class="form-control" id="quadril" name="quadril" placeholder="Ex: 96.0">
                        </div>
                        <div class="col-md-3">
                            <label for="torax" class="form-label">Tórax</label>
                            <input type="number" step="0.1" class="form-control" id="torax" name="torax" placeholder="Ex: 98.0">
                        </div>
                        <div class="col-md-3">
                            <label for="braco_direito" class="form-label">Braço Direito</label>
                            <input type="number" step="0.1" class="form-control" id="braco_direito" name="braco_direito" placeholder="Ex: 34.0">
                        </div>
                        <div class="col-md-3">
                            <label for="braco_esquerdo" class="form-label">Braço Esquerdo</label>
                            <input type="number" step="0.1" class="form-control" id="braco_esquerdo" name="braco_esquerdo" placeholder="Ex: 33.5">
                        </div>
                        <div class="col-md-3">
                            <label for="coxa_direita" class="form-label">Coxa Direita</label>
                            <input type="number" step="0.1" class="form-control" id="coxa_direita" name="coxa_direita" placeholder="Ex: 56.0">
                        </div>
                        <div class="col-md-3">
                            <label for="coxa_esquerda" class="form-label">Coxa Esquerda</label>
                            <input type="number" step="0.1" class="form-control" id="coxa_esquerda" name="coxa_esquerda" placeholder="Ex: 55.8">
                        </div>
                    </div>

                    <button type="submit" class="btn w-100 mt-2" style="background-color: #f97316; color: #fff; font-weight: bold; padding: 12px; border-radius: 8px;">
                        Salvar Avaliação Física
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
