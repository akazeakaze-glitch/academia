<?php
session_start();
require_once __DIR__ . '/../conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: ../index.php");
    exit;
}

$aluno_id = $_SESSION['aluno_id'];

// 1. Buscar dados do aluno
$stmt_aluno = $pdo->prepare("SELECT sexo, treino_atual FROM alunos WHERE id = :id");
$stmt_aluno->execute(['id' => $aluno_id]);
$aluno_dados = $stmt_aluno->fetch(PDO::FETCH_ASSOC);

$sexo_aluno = strtoupper($aluno_dados['sexo'] ?? 'M');
$treino_atual = $_GET['treino'] ?? $_POST['treino'] ?? $aluno_dados['treino_atual'] ?? 'A';

// 2. Processar envio do feedback de treino
$mensagem_sucesso = '';
$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'])) {
    $comentario = trim($_POST['comentario']);
    $treino_enviado = $_POST['treino'] ?? $treino_atual;

    if (!empty($comentario) && $aluno_id) {
        $stmt = $pdo->prepare("INSERT INTO feedback_treino (aluno_id, treino, comentario) VALUES (:aluno_id, :treino, :comentario)");
        if ($stmt->execute(['aluno_id' => $aluno_id, 'treino' => $treino_enviado, 'comentario' => $comentario])) {
            $mensagem_sucesso = "Seu feedback foi enviado ao professor com sucesso!";
        } else {
            $mensagem_erro = "Erro ao enviar o feedback. Tente novamente.";
        }
    }
}

// 3. Buscar ficha de exercícios cadastrada no banco de dados
$stmt = $pdo->prepare("SELECT * FROM fichas WHERE aluno_id = :aluno_id ORDER BY id DESC LIMIT 1");
$stmt->execute(['aluno_id' => $aluno_id]);
$ficha = $stmt->fetch(PDO::FETCH_ASSOC);

$exercicios = [];

if ($ficha) {
    try {
        $sql = "SELECT e.nome, e.grupo_muscular, fe.series, fe.repeticoes, fe.divisao, fe.carga 
                FROM fichas_exercicios fe 
                JOIN exercicios e ON fe.exercicio_id = e.id 
                WHERE fe.ficha_id = :ficha_id AND fe.divisao = :divisao";
        $stmt_ex = $pdo->prepare($sql);
        $stmt_ex->execute(['ficha_id' => $ficha['id'], 'divisao' => $treino_atual]);
        $exercicios = $stmt_ex->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $exercicios = [];
    }
}

// 4. Se não houver exercícios cadastrados no banco para esta divisão, exibe a ficha padrão de adaptação
if (empty($exercicios)) {
    $treinos_padrao = [
        'M' => [
            'A' => [
                ['grupo_muscular' => 'Quadríceps', 'nome' => 'Agachamento Smith', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Quadríceps', 'nome' => 'Leg Press 45°', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Quadríceps', 'nome' => 'Cadeira Extensora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ],
            'B' => [
                ['grupo_muscular' => 'Costas', 'nome' => 'Puxada Alta Frontal', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Costas', 'nome' => 'Remada Baixa', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Bíceps', 'nome' => 'Rosca Direta com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ],
            'C' => [
                ['grupo_muscular' => 'Peitoral', 'nome' => 'Supino Reto com Barra', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Peitoral', 'nome' => 'Supino Inclinado com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Tríceps', 'nome' => 'Tríceps Pulley', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Ombros', 'nome' => 'Desenvolvimento com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ]
        ],
        'F' => [
            'A' => [
                ['grupo_muscular' => 'Glúteos', 'nome' => 'Elevação Pélvica', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Quadríceps', 'nome' => 'Agachamento Smith', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Quadríceps', 'nome' => 'Cadeira Extensora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ],
            'B' => [
                ['grupo_muscular' => 'Posterior', 'nome' => 'Mesa Flexora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Glúteos', 'nome' => 'Coice na Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Panturrilha', 'nome' => 'Panturrilha em Pé', 'series' => '3', 'repeticoes' => '12-15', 'carga' => ''],
            ],
            'C' => [
                ['grupo_muscular' => 'Membros Superiores', 'nome' => 'Puxada Alta Frontal', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Membros Superiores', 'nome' => 'Supino Reto', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Ombros', 'nome' => 'Elevação Lateral', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ]
        ]
    ];

    $chave_sexo = ($sexo_aluno === 'F') ? 'F' : 'M';
    $exercicios = $treinos_padrao[$chave_sexo][$treino_atual] ?? [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Treino - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #ffffff; font-family: 'Segoe UI', sans-serif; padding-bottom: 90px; }
        .card-dark { background-color: #1a1a1a; border: 1px solid #333; border-radius: 12px; }
        .badge-orange { background-color: #f97316; color: #fff; font-weight: bold; }
        .card-exercicio { background-color: #242424; border: 1px solid #333; border-radius: 8px; padding: 12px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .nome-exercicio { font-weight: bold; color: #fff; font-size: 1rem; }
        .grupo-muscular { font-size: 0.8rem; color: #f97316; text-transform: uppercase; }
        .series-reps { font-size: 0.85rem; color: #aaa; }
    </style>
</head>
<body class="py-3">

    <div class="container">
        
        <!-- CABEÇALHO -->
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary">
            <h4 class="mb-0">Ficha de Treino</h4>
            <span class="badge badge-orange fs-6">Treino <?= htmlspecialchars($treino_atual) ?></span>
        </div>

        <!-- MENSAGENS DE ALERTA -->
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

        <!-- SELETOR DE DIVISÃO DE TREINO (A, B, C) -->
        <div class="d-flex gap-2 mb-4">
            <a href="meu_treino.php?treino=A" class="btn flex-fill <?= $treino_atual === 'A' ? 'btn-warning' : 'btn-outline-secondary text-white' ?>" style="font-weight: bold;">Treino A</a>
            <a href="meu_treino.php?treino=B" class="btn flex-fill <?= $treino_atual === 'B' ? 'btn-warning' : 'btn-outline-secondary text-white' ?>" style="font-weight: bold;">Treino B</a>
            <a href="meu_treino.php?treino=C" class="btn flex-fill <?= $treino_atual === 'C' ? 'btn-warning' : 'btn-outline-secondary text-white' ?>" style="font-weight: bold;">Treino C</a>
        </div>

        <!-- LISTAGEM DE EXERCÍCIOS -->
        <?php if (empty($exercicios)): ?>
            <div class="alert alert-dark text-center border-secondary my-4" role="alert" style="background-color: #1a1a1a; color: #aaa;">
                Nenhum exercício encontrado para o Treino <?= htmlspecialchars($treino_atual) ?>.
            </div>
        <?php else: ?>
            <div class="mb-4">
                <?php foreach ($exercicios as $ex): ?>
                    <div class="card-exercicio">
                        <div>
                            <div class="grupo-muscular"><?= htmlspecialchars($ex['grupo_muscular']) ?></div>
                            <div class="nome-exercicio"><?= htmlspecialchars($ex['nome']) ?></div>
                            <div class="series-reps">
                                <?= htmlspecialchars($ex['series']) ?> séries x <?= htmlspecialchars($ex['repeticoes']) ?> repetições
                                <?php if (!empty($ex['carga'])): ?>
                                    | <strong style="color: #fff;"><?= htmlspecialchars($ex['carga']) ?> kg</strong>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- FORMULÁRIO DE FEEDBACK -->
        <div class="card mt-4 mb-4" style="background-color: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 12px;">
            <div class="card-header" style="background-color: #252525; border-bottom: 1px solid #333; color: #fff; border-radius: 12px 12px 0 0;">
                <h5 class="card-title mb-0" style="font-size: 1.1rem;">Enviar Feedback ao Professor</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <!-- CAMPO OCULTO QUE GARANTE A LETRA DO TREINO (A, B, C) -->
                    <input type="hidden" name="treino" value="<?= htmlspecialchars($treino_atual) ?>">
                    
                    <div class="mb-3">
                        <label for="comentario" class="form-label" style="color: #ccc; font-weight: 500;">
                            O que achou do Treino <?= htmlspecialchars($treino_atual) ?> de hoje?
                        </label>
                        <textarea class="form-control" id="comentario" name="comentario" rows="3" placeholder="Ex: Achei o treino muito pesado nas pernas, gostaria de adicionar mais exercícios..." required style="background-color: #121212; color: #fff; border: 1px solid #444; border-radius: 8px;"></textarea>
                    </div>
                    <button type="submit" class="btn w-100" style="background-color: #f97316; color: #fff; font-weight: bold; border-radius: 8px; padding: 10px;">Enviar Mensagem</button>
                </form>
            </div>
        </div>

    </div>

    <!-- MENU DE NAVEGAÇÃO INFERIOR FIXO -->
    <div style="position: fixed; bottom: 0; left: 0; right: 0; width: 100%; background-color: #1a1a1a; border-top: 1px solid #333; display: flex; justify-content: space-around; align-items: center; padding: 10px 0; z-index: 1000;">
        <a href="painel_aluno.php" style="color: #ffffff; text-decoration: none; text-align: center; flex: 1; display: flex; flex-direction: column; align-items: center; font-size: 0.8rem; font-weight: 500;">
            <i class="bi bi-house-door" style="font-size: 1.4rem; margin-bottom: 3px; color: #f97316;"></i>
            <span>Início</span>
        </a>
        <a href="meu_treino.php" style="color: #ffffff; text-decoration: none; text-align: center; flex: 1; display: flex; flex-direction: column; align-items: center; font-size: 0.8rem; font-weight: 500;">
            <i class="bi bi-journal-check" style="font-size: 1.4rem; margin-bottom: 3px; color: #f97316;"></i>
            <span>Treino</span>
        </a>
        <a href="perfil.php" style="color: #ffffff; text-decoration: none; text-align: center; flex: 1; display: flex; flex-direction: column; align-items: center; font-size: 0.8rem; font-weight: 500;">
            <i class="bi bi-person" style="font-size: 1.4rem; margin-bottom: 3px; color: #f97316;"></i>
            <span>Perfil</span>
        </a>
    </div>

</body>
</html>
