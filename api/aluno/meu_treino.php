<?php
session_start();
require_once __DIR__ . '/../conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: ../index.php");
    exit;
}

$aluno_id = $_SESSION['aluno_id'];

$stmt_aluno = $pdo->prepare("SELECT sexo, treino_atual FROM alunos WHERE id = :id");
$stmt_aluno->execute(['id' => $aluno_id]);
$aluno_dados = $stmt_aluno->fetch(PDO::FETCH_ASSOC);

$sexo_aluno = strtoupper($aluno_dados['sexo'] ?? 'M');

$treino_atual = $_GET['treino'] ?? $_POST['treino'] ?? $aluno_dados['treino_atual'] ?? 'A';
if (!in_array($treino_atual, ['A', 'B', 'C', 'D'])) {
    $treino_atual = 'A';
}

$mensagem_sucesso = '';
$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['finalizar_com_feedback'])) {
        $comentario = trim($_POST['comentario'] ?? '');
        $treino_enviado = $_POST['treino'] ?? $treino_atual;

        if (!empty($comentario)) {
            $stmt = $pdo->prepare("INSERT INTO feedback_treino (aluno_id, treino, comentario) VALUES (:aluno_id, :treino, :comentario)");
            if ($stmt->execute(['aluno_id' => $aluno_id, 'treino' => $treino_enviado, 'comentario' => $comentario])) {
                $mensagem_sucesso = "Treino concluído e feedback enviado ao professor!";
            } else {
                $mensagem_erro = "Erro ao enviar o feedback. Tente novamente.";
            }
        } else {
            $mensagem_sucesso = "Treino concluído com sucesso!";
        }
    } elseif (isset($_POST['finalizar_sem_feedback'])) {
        $mensagem_sucesso = "Treino concluído com sucesso!";
    }
}

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
            ],
            'D' => [
                ['grupo_muscular' => 'Ombros', 'nome' => 'Desenvolvimento com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Ombros', 'nome' => 'Elevação Lateral', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'Abdômen', 'nome' => 'Abdominal Infra', 'series' => '4', 'repeticoes' => '15-20', 'carga' => ''],
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
            ],
            'D' => [
                ['grupo_muscular' => 'Abdômen & Core', 'nome' => 'Prancha Abdominal', 'series' => '3', 'repeticoes' => '45 seg', 'carga' => ''],
                ['grupo_muscular' => 'Cardio', 'nome' => 'Esteira / Elíptico', 'series' => '1', 'repeticoes' => '20 min', 'carga' => ''],
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
        
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary">
            <h4 class="mb-0">Ficha de Treino</h4>
            <span class="badge badge-orange fs-6">Treino <?= htmlspecialchars($treino_atual) ?></span>
        </div>

        <?php if ($mensagem_sucesso): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($mensagem_sucesso) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($mensagem_erro): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($mensagem_erro) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex gap-2 mb-4">
            <a href="meu_treino.php?treino=A" class="btn flex-fill <?= $treino_atual === 'A' ? 'btn-warning' : 'btn-outline-secondary text-white' ?>" style="font-weight: bold;">Treino A</a>
            <a href="meu_treino.php?treino=B" class="btn flex-fill <?= $treino_atual === 'B' ? 'btn-warning' : 'btn-outline-secondary text-white' ?>" style="font-weight: bold;">Treino B</a>
            <a href="meu_treino.php?treino=C" class="btn flex-fill <?= $treino_atual === 'C' ? 'btn-warning' : 'btn-outline-secondary text-white' ?>" style="font-weight: bold;">Treino C</a>
            <a href="meu_treino.php?treino=D" class="btn flex-fill <?= $treino_atual === 'D' ? 'btn-warning' : 'btn-outline-secondary text-white' ?>" style="font-weight: bold;">Treino D</a>
        </div>

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

        <button type="button" class="btn w-100 mt-2 p-3 mb-4" data-bs-toggle="modal" data-bs-target="#modalFeedback" style="background-color: #f97316; color: #ffffff; font-weight: bold; border-radius: 12px; font-size: 1.05rem;">
            <i class="bi bi-send-check me-1"></i> Finalizar Treino de Hoje
        </button>

    </div>

    <div class="modal fade" id="modalFeedback" tabindex="-1" aria-labelledby="modalFeedbackLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: #1a1a1a; color: #fff; border: 1px solid #333; border-radius: 12px;">
                <div class="modal-header" style="border-bottom: 1px solid #333;">
                    <h5 class="modal-title" id="modalFeedbackLabel"><i class="bi bi-check-circle-fill text-success me-2"></i>Treino Concluído!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="treino" value="<?= htmlspecialchars($treino_atual) ?>">
                        
                        <p style="color: #ddd; font-size: 0.95rem;">
                            Quer deixar um comentário sobre o <strong>Treino <?= htmlspecialchars($treino_atual) ?></strong> para o seu professor?
                        </p>
                        <div class="mb-3">
                            <textarea class="form-control" name="comentario" rows="3" placeholder="Ex: Treino muito bom, mas achei pesado no agachamento..." style="background-color: #121212; color: #fff; border: 1px solid #444; border-radius: 8px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between" style="border-top: 1px solid #333;">
                        <button type="submit" name="finalizar_sem_feedback" class="btn btn-outline-secondary" style="border-radius: 8px;">Concluir sem feedback</button>
                        <button type="submit" name="finalizar_com_feedback" class="btn" style="background-color: #f97316; color: #fff; font-weight: bold; border-radius: 8px;">Enviar Feedback</button>
                    </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
