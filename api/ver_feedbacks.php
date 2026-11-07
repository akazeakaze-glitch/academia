<?php
session_start();
require_once __DIR__ . '/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT f.id, f.comentario, f.treino, f.data_envio, a.nome AS aluno_nome
        FROM feedback_treino f
        JOIN alunos a ON f.aluno_id = a.id
        ORDER BY f.data_envio DESC";
$stmt = $pdo->query($sql);
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedbacks - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body { background-color: #121212; color: #ffffff; font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; box-shadow: 2px 0 10px rgba(0,0,0,0.5); }
        .nav-link { color: #aaaaaa; margin-bottom: 5px; border-radius: 8px; padding: 10px 15px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background-color: #f97316; color: #ffffff; font-weight: bold; }
        .card-dark { background-color: #1e1e1e; border: 1px solid #333; border-radius: 12px; }
        .badge-orange { background-color: #f97316; color: #fff; font-weight: bold; }
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
                <li class="nav-item"><a href="avaliacoes.php" class="nav-link">Avaliações Físicas</a></li>
                <li class="nav-item"><a href="ver_feedbacks.php" class="nav-link active">Feedbacks</a></li>
            </ul>
            
            <hr style="border-color: #444;">
            <a href="index.php" class="nav-link text-danger text-center">Sair do Sistema</a>
        </div>
        
        <div class="content p-5 w-100">
            <h2 class="mb-4">Feedbacks dos Alunos</h2>

            <?php if (empty($feedbacks)): ?>
                <div class="alert alert-dark text-center border-secondary mt-4" role="alert" style="background-color: #1e1e1e; color: #aaa;">
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
                                    <span class="badge badge-orange">Treino <?= htmlspecialchars($fb['treino'] ?? 'A') ?></span>
                                </div>
                                <div class="card-body">
                                    <p class="card-text mb-0" style="color: #ddd; font-size: 0.95rem; line-height: 1.5;">
                                        <?= nl2br(htmlspecialchars($fb['comentario'])) ?>
                                    </p>
                                </div>
                                <div class="card-footer text-end" style="background-color: #1e1e1e; border-top: 1px solid #2a2a2a; font-size: 0.8rem; color: #888;">
                                    <?= date('d/m/Y', strtotime($fb['data_envio'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
