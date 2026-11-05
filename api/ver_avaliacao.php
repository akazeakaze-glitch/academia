<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: avaliacoes.php");
    exit;
}

$stmt = $pdo->prepare("SELECT av.*, al.nome, al.data_nascimento FROM avaliacoes av JOIN alunos al ON av.aluno_id = al.id WHERE av.id = :id");
$stmt->execute(['id' => $_GET['id']]);
$avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$avaliacao) {
    header("Location: avaliacoes.php");
    exit;
}

$imc = $avaliacao['peso'] / ($avaliacao['altura'] * $avaliacao['altura']);
$imc_formatado = number_format($imc, 1, ',', '.');

if ($imc < 18.5) { $classificacao = "Abaixo do peso"; $cor = "text-info"; }
elseif ($imc < 24.9) { $classificacao = "Peso normal"; $cor = "text-success"; }
elseif ($imc < 29.9) { $classificacao = "Sobrepeso"; $cor = "text-warning"; }
else { $classificacao = "Obesidade"; $cor = "text-danger"; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Avaliação - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #ffffff; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; }
        .nav-link { color: #aaaaaa; padding: 10px 15px; margin-bottom: 5px; border-radius: 8px; }
        .nav-link:hover, .nav-link.active { background-color: #f97316; color: #ffffff; }
        .info-box { background-color: #2a2a2a; padding: 20px; border-radius: 10px; border-left: 5px solid #f97316; margin-bottom: 20px; }
        
        /* CORREÇÃO DAS CORES: Tudo branco ou cinza claro! */
        .info-label { color: #cccccc; font-size: 0.9em; text-transform: uppercase; font-weight: bold; }
        .info-value { font-size: 1.2em; font-weight: bold; color: #ffffff; }
        .texto-branco { color: #ffffff !important; }
        .texto-cinza { color: #cccccc !important; }
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Relatório de Avaliação Física</h2>
                
                <div>
                    <a href="editar_avaliacao.php?id=<?php echo $avaliacao['id']; ?>" class="btn btn-primary me-2">Editar</a>
                    <a href="excluir_avaliacao.php?id=<?php echo $avaliacao['id']; ?>" class="btn btn-danger me-2" onclick="return confirm('Tem certeza que deseja excluir esta avaliação?');">Excluir</a>
                    <a href="avaliacoes.php" class="btn btn-outline-light">Voltar</a>
                </div>
            </div>
            
            <div class="card p-4" style="background-color: #1e1e1e; border: none; border-radius: 10px;">
                <h4 style="color: #f97316;"><?php echo $avaliacao['nome']; ?></h4>
                <p class="texto-cinza">Data da Avaliação: <?php echo date('d/m/Y', strtotime($avaliacao['data_avaliacao'])); ?></p>
                
                <div class="row mt-4">
                    <div class="col-md-3"><div class="info-box"><div class="info-label">Peso</div><div class="info-value"><?php echo $avaliacao['peso']; ?> kg</div></div></div>
                    <div class="col-md-3"><div class="info-box"><div class="info-label">Altura</div><div class="info-value"><?php echo $avaliacao['altura']; ?> m</div></div></div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <div class="info-label">IMC</div>
                            <div class="info-value <?php echo $cor; ?>"><?php echo $imc_formatado; ?> (<?php echo $classificacao; ?>)</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <div class="info-label">% de Gordura</div>
                            <div class="info-value">
                                <?php echo !empty($avaliacao['percentual_gordura']) ? $avaliacao['percentual_gordura'] . '%' : '<span class="texto-cinza">Não inf.</span>'; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="mt-4 border-bottom border-secondary pb-2 texto-cinza">Anamnese e Perfil</h5>
                <div class="row mt-3">
                    <div class="col-md-12 mb-3">
                        <span class="info-label">Como descreve o corpo:</span>
                        <!-- TEXTO BRANCO PARA DAR DESTAQUE -->
                        <p class="fs-5 mt-1 texto-branco"><?php echo $avaliacao['percepcao_corporal']; ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <span class="info-label">Doenças Pré-existentes:</span>
                        <p class="fs-5 mt-1 texto-branco"><?php echo !empty($avaliacao['doencas_preexistentes']) ? $avaliacao['doencas_preexistentes'] : 'Nenhuma relatada'; ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <span class="info-label">Limitações Físicas / Dores:</span>
                        <p class="fs-5 mt-1 text-danger fw-bold"><?php echo !empty($avaliacao['limitacoes_fisicas']) ? $avaliacao['limitacoes_fisicas'] : 'Nenhuma relatada'; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>