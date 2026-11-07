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
$treino_atual = $aluno_dados['treino_atual'] ?? 'A';
if (!in_array($treino_atual, ['A', 'B', 'C', 'D'])) {
    $treino_atual = 'A';
}

$mensagem_sucesso = '';
$mensagem_erro = '';
$treino_concluido_msg = '';

// Próximo treino na sequência (A -> B -> C -> D -> A)
$proximo_treino_map = ['A' => 'B', 'B' => 'C', 'C' => 'D', 'D' => 'A'];

// Descrições dos próximos treinos para o banner verde
$descricoes_treinos_M = [
    'A' => 'Peito, Ombros, Tríceps',
    'B' => 'Costas, Bíceps',
    'C' => 'Quadríceps, Posterior, Panturrilha',
    'D' => 'Superior Completo'
];

$descricoes_treinos_F = [
    'A' => 'Glúteos, Posterior, Panturrilha',
    'B' => 'Costas, Ombros, Bíceps, Tríceps',
    'C' => 'Quadríceps, Glúteos, Abdômen',
    'D' => 'Membros Inferiores e Core'
];

$desc_map = ($sexo_aluno === 'F') ? $descricoes_treinos_F : $descricoes_treinos_M;

// 2. Processar finalização do treino e envio do feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comentario = trim($_POST['comentario'] ?? '');
    $treino_enviado = $treino_atual;

    if (isset($_POST['finalizar_com_feedback']) && !empty($comentario)) {
        $stmt_fb = $pdo->prepare("INSERT INTO feedback_treino (aluno_id, treino, comentario) VALUES (:aluno_id, :treino, :comentario)");
        $stmt_fb->execute(['aluno_id' => $aluno_id, 'treino' => $treino_enviado, 'comentario' => $comentario]);
    }

    // Avançar para o próximo treino no banco de dados
    $proximo = $proximo_treino_map[$treino_atual];
    $stmt_up = $pdo->prepare("UPDATE alunos SET treino_atual = :proximo WHERE id = :id");
    $stmt_up->execute(['proximo' => $proximo, 'id' => $aluno_id]);

    $treino_concluido_msg = "Parabéns! Treino {$treino_atual} concluído. O seu próximo será o Treino {$proximo} ({$desc_map[$proximo]}).";
    $treino_atual = $proximo;
}

// 3. Estruturas Padrão Masculinas e Femininas
$treinos_padrao = [
    'M' => [
        'A' => [
            ['grupo_muscular' => 'PEITO', 'nome' => 'Supino Inclinado com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'PEITO', 'nome' => 'Supino Reto Máquina', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'PEITO', 'nome' => 'Crucifixo na Máquina', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'OMBROS', 'nome' => 'Desenvolvimento na Máquina', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'OMBROS', 'nome' => 'Elevação Lateral Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'TRÍCEPS', 'nome' => 'Tríceps com Corda na Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => '']
        ],
        'B' => [
            ['grupo_muscular' => 'COSTAS', 'nome' => 'Puxada Alta Frontal', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'COSTAS', 'nome' => 'Remada Baixa', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'COSTAS', 'nome' => 'Pulldown com Corda', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'BÍCEPS', 'nome' => 'Rosca Direta com Barra', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'BÍCEPS', 'nome' => 'Rosca Alternada com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => '']
        ],
        'C' => [
            ['grupo_muscular' => 'QUADRÍCEPS', 'nome' => 'Agachamento Smith', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'QUADRÍCEPS', 'nome' => 'Leg Press 45°', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'QUADRÍCEPS', 'nome' => 'Cadeira Extensora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'POSTERIOR', 'nome' => 'Mesa Flexora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'POSTERIOR', 'nome' => 'Cadeira Flexora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'PANTURRILHA', 'nome' => 'Panturrilha na Máquina', 'series' => '3', 'repeticoes' => '12-15', 'carga' => '']
        ],
        'D' => [
            ['grupo_muscular' => 'PEITO', 'nome' => 'Supino Reto com Barra', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'COSTAS', 'nome' => 'Remada Curvada', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'BÍCEPS', 'nome' => 'Rosca Martelo', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'TRÍCEPS', 'nome' => 'Tríceps Testa', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'OMBROS', 'nome' => 'Elevação Frontal', 'series' => '3', 'repeticoes' => '10-12', 'carga' => '']
        ]
    ],
    'F' => [
        'A' => [
            ['grupo_muscular' => 'GLÚTEOS', 'nome' => 'Elevação Pélvica na Máquina', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'GLÚTEOS', 'nome' => 'Coice na Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'GLÚTEOS', 'nome' => 'Cadeira Abdutora', 'series' => '3', 'repeticoes' => '12-15', 'carga' => ''],
            ['grupo_muscular' => 'POSTERIOR', 'nome' => 'Mesa Flexora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'POSTERIOR', 'nome' => 'Cadeira Flexora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'PANTURRILHA', 'nome' => 'Panturrilha no Leg Press', 'series' => '3', 'repeticoes' => '12-15', 'carga' => '']
        ],
        'B' => [
            ['grupo_muscular' => 'COSTAS', 'nome' => 'Puxada Alta Frontal', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'COSTAS', 'nome' => 'Remada Baixa', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'COSTAS', 'nome' => 'Pulldown com Corda', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'OMBROS', 'nome' => 'Elevação Lateral Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'OMBROS', 'nome' => 'Desenvolvimento com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'BÍCEPS', 'nome' => 'Rosca Direta com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'TRÍCEPS', 'nome' => 'Tríceps com Corda na Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => '']
        ],
        'C' => [
            ['grupo_muscular' => 'QUADRÍCEPS', 'nome' => 'Agachamento Smith', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'QUADRÍCEPS', 'nome' => 'Leg Press 45°', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'QUADRÍCEPS', 'nome' => 'Cadeira Extensora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'GLÚTEOS', 'nome' => 'Elevação Pélvica', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'GLÚTEOS', 'nome' => 'Glúteo Caneleira', 'series' => '3', 'repeticoes' => '12-15', 'carga' => ''],
            ['grupo_muscular' => 'ABDÔMEN', 'nome' => 'Abdominal Infra', 'series' => '4', 'repeticoes' => '15-20', 'carga' => '']
        ],
        'D' => [
            ['grupo_muscular' => 'GLÚTEOS', 'nome' => 'Agachamento Búlgaro', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'GLÚTEOS', 'nome' => 'Coice na Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'POSTERIOR', 'nome' => 'Stiff com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'POSTERIOR', 'nome' => 'Mesa Flexora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['grupo_muscular' => 'PANTURRILHA', 'nome' => 'Panturrilha em Pé', 'series' => '3', 'repeticoes' => '12-15', 'carga' => ''],
            ['grupo_muscular' => 'CORE', 'nome' => 'Prancha Abdominal', 'series' => '3', 'repeticoes' => '45 seg', 'carga' => '']
        ]
    ]
];

// Buscar exercícios do banco ou aplicar o treino padrão
$stmt_ficha = $pdo->prepare("SELECT * FROM fichas WHERE aluno_id = :aluno_id ORDER BY id DESC LIMIT 1");
$stmt_ficha->execute(['aluno_id' => $aluno_id]);
$ficha = $stmt_ficha->fetch(PDO::FETCH_ASSOC);

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
    $chave_sexo = ($sexo_aluno === 'F') ? 'F' : 'M';
    $exercicios = $treinos_padrao[$chave_sexo][$treino_atual] ?? [];
}

$nome_ficha = ($sexo_aluno === 'F') ? 'Adaptação Feminina (Descanso: 1m30s a 2m)' : 'Adaptação Masculina (Descanso: 1m30s a 2m)';
$data_vencimento = date('d/m/Y', strtotime('+30 days'));
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
        body { background-color: #111111; color: #ffffff; font-family: 'Segoe UI', sans-serif; padding-bottom: 90px; }
        
        .titulo-ficha { color: #f59e0b; font-size: 1.15rem; font-weight: 700; text-align: center; margin-top: 10px; margin-bottom: 6px; }
        .pill-vencimento { background-color: #1c1917; border: 1px solid #78350f; color: #f97316; font-size: 0.75rem; padding: 3px 12px; border-radius: 6px; display: inline-block; }
        
        /* BANNER VERDE DE PARABÉNS (FOTO 2) */
        .banner-concluido { background-color: #22c55e; color: #ffffff; font-weight: 700; font-size: 0.95rem; border-radius: 10px; padding: 12px 16px; text-align: center; margin-top: 15px; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2); }
        
        .titulo-treino { color: #f97316; font-size: 1.4rem; font-weight: 800; text-align: center; margin-top: 15px; margin-bottom: 20px; }
        
        /* SELETOR DE ABAS ABCD BLOQUEADAS PARA ILUSTRAR PROGRESSÃO */
        .nav-abcd { display: flex; gap: 8px; justify-content: center; margin-bottom: 20px; }
        .btn-abcd { background-color: #1a1a1a; color: #666; border: 1px solid #333; border-radius: 8px; padding: 6px 16px; font-weight: bold; text-decoration: none; cursor: default; }
        .btn-abcd.active { background-color: #f97316; color: #ffffff; border-color: #f97316; }

        /* CARD DE EXERCÍCIO ORIGINAL COM BORDA LARANJA (FOTO 1 E 2) */
        .card-exercicio-original { background-color: #1e1e1e; border: 1px solid #2a2a2a; border-left: 5px solid #f97316; border-radius: 12px; padding: 14px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; }
        .tag-grupo { color: #f97316; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .nome-ex { color: #ffffff; font-size: 1rem; font-weight: 700; margin-top: 2px; margin-bottom: 4px; }
        .detalhes-ex { color: #888888; font-size: 0.85rem; }

        /* BOTAO CÍRCULO DE CHECK VERDE DA FOTO 2 */
        .circle-check-btn { width: 28px; height: 28px; border-radius: 50%; border: 2px solid #555; background-color: transparent; color: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; padding: 0; }
        .circle-check-btn.checked { background-color: #22c55e; border-color: #22c55e; color: #ffffff; box-shadow: 0 0 10px rgba(34, 197, 94, 0.4); }

        /* BOTÃO MARROM DA FOTO 2 */
        .btn-finalizar-original { background-color: #6d2800; color: #ffedd5; border: 1px solid #9a3412; font-weight: 700; border-radius: 12px; padding: 14px; font-size: 1rem; width: 100%; margin-top: 15px; margin-bottom: 25px; transition: 0.2s; opacity: 0.5; pointer-events: none; }
        .btn-finalizar-original.habilitado { opacity: 1; pointer-events: auto; background-color: #f97316; color: #ffffff; border-color: #ea580c; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3); }
    </style>
</head>
<body>

    <div class="container" style="max-width: 600px;">
        
        <!-- CABEÇALHO DA PÁGINA (FOTO 1 E 2) -->
        <div class="titulo-ficha"><?= htmlspecialchars($nome_ficha) ?></div>
        <div class="text-center mb-2">
            <span class="pill-vencimento"><i class="bi bi-calendar-event me-1"></i> Vence a: <?= $data_vencimento ?></span>
        </div>

        <!-- BANNER VERDE AO CONCLUIR O TREINO (FOTO 2) -->
        <?php if ($treino_concluido_msg): ?>
            <div class="banner-concluido">
                <i class="bi bi-trophy-fill me-2"></i><?= htmlspecialchars($treino_concluido_msg) ?>
            </div>
        <?php endif; ?>

        <!-- ABAS INDICATIVAS DO TREINO ATUAL -->
        <div class="nav-abcd mt-3">
            <span class="btn-abcd <?= $treino_atual === 'A' ? 'active' : '' ?>">A</span>
            <span class="btn-abcd <?= $treino_atual === 'B' ? 'active' : '' ?>">B</span>
            <span class="btn-abcd <?= $treino_atual === 'C' ? 'active' : '' ?>">C</span>
            <span class="btn-abcd <?= $treino_atual === 'D' ? 'active' : '' ?>">D</span>
        </div>

        <!-- TÍTULO DO TREINO CENTRALIZADO (FOTO 1 E 2) -->
        <div class="titulo-treino">Treino <?= htmlspecialchars($treino_atual) ?></div>

        <!-- CARDS DOS EXERCÍCIOS -->
        <div class="mb-4" id="listaExercicios">
            <?php foreach ($exercicios as $index => $ex): ?>
                <div class="card-exercicio-original">
                    <div>
                        <div class="tag-grupo"><?= htmlspecialchars($ex['grupo_muscular']) ?></div>
                        <div class="nome-ex"><?= htmlspecialchars($ex['nome']) ?></div>
                        <div class="detalhes-ex">
                            <i class="bi bi-arrow-repeat me-1"></i><?= htmlspecialchars($ex['series']) ?> x <?= htmlspecialchars($ex['repeticoes']) ?>
                            <?php if (!empty($ex['carga'])): ?>
                                | <?= htmlspecialchars($ex['carga']) ?> kg
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="circle-check-btn" onclick="toggleExercicio(this)">
                            <i class="bi bi-check-lg" style="font-size: 1.1rem; line-height: 1;"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- BOTÃO PARA ABRIR O MODAL DE FEEDBACK (IGUAL À FOTO 2) -->
        <button type="button" id="btnFinalizar" class="btn btn-finalizar-original" data-bs-toggle="modal" data-bs-target="#modalFeedback">
            <i class="bi bi-send-check me-2"></i>Finalizar Treino de Hoje
        </button>

    </div>

    <!-- MODAL DE FEEDBACK AO CONCLUIR -->
    <div class="modal fade" id="modalFeedback" tabindex="-1" aria-labelledby="modalFeedbackLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: #1a1a1a; color: #fff; border: 1px solid #333; border-radius: 12px;">
                <div class="modal-header" style="border-bottom: 1px solid #333;">
                    <h5 class="modal-title" id="modalFeedbackLabel"><i class="bi bi-check-circle-fill text-success me-2"></i>Treino Concluído!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <p style="color: #ddd; font-size: 0.95rem;">
                            Quer deixar um comentário sobre o <strong>Treino <?= htmlspecialchars($treino_atual) ?></strong> para o seu professor?
                        </p>
                        <div class="mb-3">
                            <textarea class="form-control" name="comentario" rows="3" placeholder="Ex: Achei o treino muito bom, mas a carga do supino estava leve..." style="background-color: #121212; color: #fff; border: 1px solid #444; border-radius: 8px;"></textarea>
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

    <!-- MENU INFERIOR FIXO -->
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
    <script>
        function toggleExercicio(btn) {
            btn.classList.toggle('checked');
            verificarConclusao();
        }

        function verificarConclusao() {
            const todosBotoes = document.querySelectorAll('.circle-check-btn');
            const marcados = document.querySelectorAll('.circle-check-btn.checked');
            const btnFinalizar = document.getElementById('btnFinalizar');

            if (todosBotoes.length > 0 && marcados.length === todosBotoes.length) {
                btnFinalizar.classList.add('habilitado');
            } else {
                btnFinalizar.classList.remove('habilitado');
            }
        }
    </script>

</body>
</html>
