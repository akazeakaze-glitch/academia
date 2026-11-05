<?php
session_start();
require 'conexao.php';

// Proteção da página
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aluno_id = $_POST['aluno_id'];
    $data_avaliacao = date('Y-m-d'); 
    $peso = $_POST['peso'];
    $altura = $_POST['altura'];
    
    $percentual_gordura = !empty($_POST['percentual_gordura']) ? $_POST['percentual_gordura'] : NULL;
    
    $percepcao_corporal = $_POST['percepcao_corporal'];
    $doencas_preexistentes = $_POST['doencas_preexistentes'];
    $limitacoes_fisicas = $_POST['limitacoes_fisicas'];

    $sql = "INSERT INTO avaliacoes (aluno_id, data_avaliacao, peso, altura, percentual_gordura, percepcao_corporal, doencas_preexistentes, limitacoes_fisicas) 
            VALUES (:aluno_id, :data_avaliacao, :peso, :altura, :percentual_gordura, :percepcao_corporal, :doencas_preexistentes, :limitacoes_fisicas)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'aluno_id' => $aluno_id,
        'data_avaliacao' => $data_avaliacao,
        'peso' => $peso,
        'altura' => $altura,
        'percentual_gordura' => $percentual_gordura,
        'percepcao_corporal' => $percepcao_corporal,
        'doencas_preexistentes' => $doencas_preexistentes,
        'limitacoes_fisicas' => $limitacoes_fisicas
    ]);

    header("Location: avaliacoes.php");
    exit;
}

$stmt_alunos = $pdo->query("SELECT id, nome FROM alunos ORDER BY nome ASC");
$alunos = $stmt_alunos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nova Avaliação - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #ffffff; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; box-shadow: 2px 0 10px rgba(0,0,0,0.5); }
        .nav-link { color: #aaaaaa; padding: 10px 15px; margin-bottom: 5px; border-radius: 8px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background-color: #f97316; color: #ffffff; font-weight: bold; }
        
        .form-control, .form-select { 
            background-color: #2a2a2a !important; 
            border: 1px solid #444 !important; 
            color: #ffffff !important; 
        }
        .form-control:focus, .form-select:focus { 
            background-color: #333333 !important; 
            border-color: #f97316 !important; 
            color: #ffffff !important; 
            box-shadow: 0 0 5px rgba(249, 115, 22, 0.5); 
        }
        .form-control::placeholder { color: #888888; }
        
        /* Cor clara para as labels */
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
            <h2 class="mb-4">Registrar Avaliação e Anamnese</h2>
            
            <div class="card p-4" style="background-color: #1e1e1e; border: none; border-radius: 10px;">
                <form action="cadastrar_avaliacao.php" method="POST">
                    
                    <h5 style="color: #f97316; border-bottom: 1px solid #333; padding-bottom: 10px; margin-bottom: 20px;">1. Medidas Corporais</h5>
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Selecione o Aluno</label>
                            <select name="aluno_id" class="form-select" required>
                                <option value="">Escolha o aluno...</option>
                                <?php foreach ($alunos as $aluno): ?>
                                    <option value="<?php echo $aluno['id']; ?>"><?php echo $aluno['nome']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Peso (kg)</label>
                            <input type="number" step="0.01" name="peso" class="form-control" placeholder="Ex: 80.5" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Altura (m)</label>
                            <input type="number" step="0.01" name="altura" class="form-control" placeholder="Ex: 1.75" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">% Gordura (Opcional)</label>
                            <input type="number" step="0.1" name="percentual_gordura" class="form-control" placeholder="Deixe em branco se não souber">
                        </div>
                    </div>

                    <h5 style="color: #f97316; border-bottom: 1px solid #333; padding-bottom: 10px; margin-top: 20px; margin-bottom: 20px;">2. Perfil e Anamnese</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Como o aluno descreve o próprio corpo hoje?</label>
                            <input type="text" name="percepcao_corporal" class="form-control" placeholder="Ex: Sinto que ganhei peso, tenho facilidade em ganhar músculos..." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Doenças Pré-existentes</label>
                            <textarea name="doencas_preexistentes" class="form-control" rows="3" placeholder="Diabetes, pressão alta, asma, etc. Se não houver, deixe em branco."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dores, Cirurgias ou Limitações</label>
                            <textarea name="limitacoes_fisicas" class="form-control" rows="3" placeholder="Ex: Dor crônica na lombar, cirurgia no joelho esquerdo..."></textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-3">
                        <button type="submit" class="btn" style="background-color: #f97316; color: white; font-weight: bold; padding: 10px 25px;">Salvar Avaliação</button>
                        <a href="avaliacoes.php" class="btn btn-outline-light" style="padding: 10px 25px;">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>