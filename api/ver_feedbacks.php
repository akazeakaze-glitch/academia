<?php
session_start();
require_once __DIR__ . '/conexao.php';

// Consulta os feedbacks ordenados do mais recente para o mais antigo
$sql = "SELECT f.id, f.comentario, f.data_envio, a.nome AS aluno_nome, a.treino_atual
        FROM feedback_treino f
        JOIN alunos a ON f.aluno_id = a.id
        ORDER BY f.data_envio DESC";
$stmt = $pdo->query($sql);
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedbacks dos Alunos - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #ffffff; font-family: sans-serif; }
        .card-dark { background-color: #1a1a1a; border: 1px solid #333; border-radius: 12px; }
        .badge-orange { background-color: #f97316; color: #fff; font-weight: bold; }
    </style>
</head>
<body class="py-4">
    <div class="container">
        
        <!-- CABEÇALHO DA PÁGINA (SEM O ÍCONE) -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary">
            <h3 class="mb-0">Feedbacks dos Alunos</h3>
            <a href="painel.php" class="btn text-white" style="background-color: #252525; border: 1px solid #444; border-radius: 8px;">
                <i class="bi bi-arrow-left"></i> Voltar ao Painel
            </a>
        </div>

        <!-- LISTAGEM DOS FEEDBACKS -->
        <?php if (empty($feedbacks)): ?>
            <div class="alert alert-dark text-center border-secondary mt-4" role="alert" style="background-color: #1a1a1a; color: #aaa;">
                <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                Nenhum feedback enviado pelos alunos até o momento.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($feedbacks as $fb): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card card-dark h-100">
                            <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #252525; border-bottom: 1px solid #333; color: #fff; border-radius: 12px 12px 0 0;">
                                <div>
                                    <i class="bi bi-person-circle me-1" style="color: #f97316;"></i>
                                    <strong><?= htmlspecialchars($fb['aluno_nome']) ?></strong>
                                </div>
                                <span class="badge badge-orange">Treino <?= htmlspecialchars($fb['treino_atual'] ?? 'A') ?></span>
                            </div>
                            <div class="card-body">
                                <p class="card-text mb-0" style="color: #ddd; font-size: 0.95rem; line-height: 1.5;">
                                    <?= nl2br(htmlspecialchars($fb['comentario'])) ?>
                                </p>
                            </div>
                            <!-- EXIBE APENAS A DATA (DD/MM/AAAA) -->
                            <div class="card-footer text-end" style="background-color: #1a1a1a; border-top: 1px solid #2a2a2a; font-size: 0.8rem; color: #888;">
                                <i class="bi bi-clock me-1"></i> <?= date('d/m/Y', strtotime($fb['data_envio'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>
