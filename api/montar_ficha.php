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
    $nome_ficha = $_POST['nome_ficha'];
    $data_vencimento = $_POST['data_vencimento'];

    $exercicios_selecionados = isset($_POST['exercicios']) ? $_POST['exercicios'] : [];
    $series = $_POST['series'];
    $repeticoes = $_POST['repeticoes'];

    if (empty($aluno_id) || empty($nome_ficha) || count($exercicios_selecionados) == 0) {
        $erro = "Preencha os dados da ficha e selecione pelo menos um exercício!";
    } else {
        try {
            $sqlFicha = "INSERT INTO fichas (aluno_id, nome_ficha, data_vencimento) VALUES (:aluno, :nome, :vencimento)";
            $stmtFicha = $pdo->prepare($sqlFicha);
            $stmtFicha->execute([
                'aluno' => $aluno_id,
                'nome' => $nome_ficha,
                'vencimento' => $data_vencimento
            ]);
            
            $ficha_id = $pdo->lastInsertId();

            $sqlEx = "INSERT INTO fichas_exercicios (ficha_id, exercicio_id, series, repeticoes) VALUES (:ficha, :exercicio, :series, :reps)";
            $stmtEx = $pdo->prepare($sqlEx);

            foreach ($exercicios_selecionados as $ex_id) {
                $s = !empty($series[$ex_id]) ? $series[$ex_id] : '3';
                $r = !empty($repeticoes[$ex_id]) ? $repeticoes[$ex_id] : '12';
                
                $stmtEx->execute([
                    'ficha' => $ficha_id,
                    'exercicio' => $ex_id,
                    'series' => $s,
                    'reps' => $r
                ]);
            }

            $sucesso = "Ficha montada e enviada para o aplicativo do aluno com sucesso!";
        } catch (PDOException $e) {
            $erro = "Erro ao salvar a ficha. Verifique se as tabelas estão corretas. Erro: " . $e->getMessage();
        }
    }
}

$stmtAlunos = $pdo->query("SELECT id, nome FROM alunos ORDER BY nome ASC");
$alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);

$stmtExercicios = $pdo->query("SELECT id, nome, grupo_muscular FROM exercicios ORDER BY grupo_muscular ASC, nome ASC");
$exercicios = $stmtExercicios->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Montar Ficha - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #121212; color: #ffffff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background-color: #1e1e1e; border-bottom: 2px solid #f97316; }
        .card-form { background-color: #1e1e1e; border-radius: 12px; padding: 25px; margin-top: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        .form-control, .form-select { background-color: #2a2a2a; border: 1px solid #444; color: white; }
        .form-control:focus, .form-select:focus { background-color: #333; border-color: #f97316; color: white; box-shadow: none; }
        .btn-salvar { background-color: #f97316; color: white; font-weight: bold; border: none; padding: 12px 30px; border-radius: 8px; transition: 0.3s; }
        .btn-salvar:hover { background-color: #e06512; color: white; }
        
        .tabela-exercicios { background-color: #2a2a2a; border-radius: 8px; padding: 15px; max-height: 400px; overflow-y: auto; }
        .ex-item { border-bottom: 1px solid #444; padding: 10px 0; display: flex; align-items: center; justify-content: space-between; }
        .ex-item:last-child { border-bottom: none; }
        .inputs-treino { display: flex; gap: 10px; width: 200px; }
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

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card-form">
                    <h3 class="mb-4 text-center" style="color: #f97316;">
                        <i class="bi bi-clipboard2-check"></i> Montar Ficha de Treino
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

                    <form action="montar_ficha.php" method="POST">
                        
                        <!-- DADOS DA FICHA -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted mb-1">Selecione o Aluno</label>
                                <select name="aluno_id" class="form-select" required>
                                    <option value="">Escolha um aluno...</option>
                                    <?php foreach ($alunos as $aluno): ?>
                                        <option value="<?php echo $aluno['id']; ?>"><?php echo $aluno['nome']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted mb-1">Nome da Ficha (Ex: Treino A, FullBody)</label>
                                <input type="text" name="nome_ficha" class="form-control" placeholder="Digite o nome..." required>
                            </div>
                            <div class="col-md-12">
                                <label class="text-muted mb-1">Data de Vencimento do Treino</label>
                                <input type="date" name="data_vencimento" class="form-control" required>
                            </div>
                        </div>

                        <hr style="border-color: #444;">

                        <h5 class="mb-3 mt-4">Selecione os Exercícios:</h5>
                        <p class="text-muted" style="font-size: 0.9em;">Marque a caixinha e defina as séries e repetições (Ex: 3 séries, 12 reps).</p>
                        
                        <div class="tabela-exercicios mb-4">
                            <?php if (count($exercicios) == 0): ?>
                                <div class="text-center text-muted py-3">Nenhum exercício cadastrado no banco de dados.</div>
                            <?php else: ?>
                                <?php foreach ($exercicios as $ex): ?>
                                    <div class="ex-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="exercicios[]" value="<?php echo $ex['id']; ?>" id="ex_<?php echo $ex['id']; ?>">
                                            <label class="form-check-label" for="ex_<?php echo $ex['id']; ?>">
                                                <strong><?php echo $ex['nome']; ?></strong> <br>
                                                <small class="text-warning"><?php echo $ex['grupo_muscular']; ?></small>
                                            </label>
                                        </div>
                                        <div class="inputs-treino">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-dark text-light border-secondary">Séries</span>
                                                <input type="number" name="series[<?php echo $ex['id']; ?>]" class="form-control border-secondary text-light bg-dark" placeholder="3">
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-dark text-light border-secondary">Reps</span>
                                                <input type="text" name="repeticoes[<?php echo $ex['id']; ?>]" class="form-control border-secondary text-light bg-dark" placeholder="12">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn-salvar">
                                <i class="bi bi-send-check"></i> Salvar e Enviar para o Aluno
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>