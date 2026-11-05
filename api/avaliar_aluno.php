<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'professor') {
    header("Location: index.php");
    exit;
}

$sucesso = '';
$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aluno_id = $_POST['aluno_id'];
    $data_avaliacao = $_POST['data_avaliacao'];
    
    $peso = (float) str_replace(',', '.', $_POST['peso']);
    $altura = (float) str_replace(',', '.', $_POST['altura']);

    if (empty($aluno_id) || empty($peso) || empty($altura) || empty($data_avaliacao)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        $imc = $peso / ($altura * $altura);

        try {
            $sql = "INSERT INTO avaliacoes (aluno_id, peso, altura, imc, data_avaliacao) 
                    VALUES (:aluno_id, :peso, :altura, :imc, :data_avaliacao)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'aluno_id' => $aluno_id,
                'peso' => $peso,
                'altura' => $altura,
                'imc' => round($imc, 2),
                'data_avaliacao' => $data_avaliacao
            ]);
            
            $imc_formatado = number_format($imc, 2, ',', '.');
            $sucesso = "Avaliação registrada com sucesso! O sistema calculou o IMC como: <strong>$imc_formatado</strong>";
        } catch (PDOException $e) {
            $erro = "Erro ao salvar na base de dados: " . $e->getMessage();
        }
    }
}

$stmtAlunos = $pdo->query("SELECT id, nome FROM alunos ORDER BY nome ASC");
$alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliar Aluno - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #121212; color: #ffffff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background-color: #1e1e1e; border-bottom: 2px solid #f97316; }
        .card-form { background-color: #1e1e1e; border-radius: 12px; padding: 30px; margin-top: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); border-top: 4px solid #f97316; }
        .form-control, .form-select { background-color: #2a2a2a; border: 1px solid #444; color: white; padding: 12px; }
        .form-control:focus, .form-select:focus { background-color: #333; border-color: #f97316; color: white; box-shadow: none; }
        .btn-salvar { background-color: #f97316; color: white; font-weight: bold; border: none; padding: 12px; border-radius: 8px; width: 100%; transition: 0.3s; margin-top: 15px; }
        .btn-salvar:hover { background-color: #e06512; color: white; }
        .input-group-text { background-color: #f97316; color: white; border: none; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" style="color: #f97316;" href="painel.php">
                <i class="bi bi-arrow-left me-2"></i> Voltar ao Painel
            </a>
            <span class="text-light">Prof. <?php echo $_SESSION['usuario_nome']; ?></span>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card-form">
                    <h3 class="mb-4 text-center">
                        <i class="bi bi-clipboard2-pulse text-warning"></i> Nova Avaliação Física
                    </h3>

                    <?php if ($sucesso): ?>
                        <div class="alert alert-success" style="background-color: #22c55e; color: white; border: none;">
                            <i class="bi bi-check-circle"></i> <?php echo $sucesso; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($erro): ?>
                        <div class="alert alert-danger" style="background-color: #ef4444; color: white; border: none;">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo $erro; ?>
                        </div>
                    <?php endif; ?>

                    <form action="avaliar_aluno.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="text-muted mb-1">Selecione o Aluno</label>
                            <select name="aluno_id" class="form-select" required>
                                <option value="">Escolha um aluno...</option>
                                <?php foreach ($alunos as $aluno): ?>
                                    <option value="<?php echo $aluno['id']; ?>"><?php echo $aluno['nome']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted mb-1">Peso (kg)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" name="peso" class="form-control" placeholder="Ex: 75.5" required>
                                    <span class="input-group-text">kg</span>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="text-muted mb-1">Altura (m)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="altura" class="form-control" placeholder="Ex: 1.75" required>
                                    <span class="input-group-text">m</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted mb-1">Data da Avaliação</label>
                            <input type="date" name="data_avaliacao" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <button type="submit" class="btn-salvar">
                            <i class="bi bi-calculator"></i> Calcular IMC e Salvar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>