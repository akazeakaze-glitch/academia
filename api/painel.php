<?php
session_start();
require_once __DIR__ . '/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$stmt_alunos = $pdo->query("SELECT COUNT(*) AS total FROM alunos");
$total_alunos = $stmt_alunos->fetch(PDO::FETCH_ASSOC)['total'];

$hoje = date('Y-m-d');
$stmt_fichas = $pdo->prepare("SELECT COUNT(*) AS total FROM fichas WHERE data_vencimento < :hoje");
$stmt_fichas->execute(['hoje' => $hoje]);
$fichas_vencidas = $stmt_fichas->fetch(PDO::FETCH_ASSOC)['total'];

// Buscar contagem de feedbacks recebidos
try {
    $stmt_feedbacks = $pdo->query("SELECT COUNT(*) AS total FROM feedback_treino");
    $total_feedbacks = $stmt_feedbacks->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    $total_feedbacks = 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body { background-color: #121212; color: #ffffff; font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; box-shadow: 2px 0 10px rgba(0,0,0,0.5); }
        .nav-link { color: #aaaaaa; margin-bottom: 5px; border-radius: 8px; padding: 10px 15px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background-color: #f97316; color: #ffffff; font-weight: bold; }
        
        .card-dash { background-color: #1e1e1e; border: none; border-left: 4px solid #f97316; border-radius: 10px; transition: transform 0.2s; }
        .card-dash:hover { transform: translateY(-3px); }
        
        .titulo-card { color: #e0e0e0; font-weight: 600; text-transform: uppercase; font-size: 0.85em; letter-spacing: 1px; }
    </style>
</head>
<body>

    <div class="d-flex">
        <div class="sidebar p-3 d-flex flex-column">
            <h4 class="text-center mt-3 mb-4" style="color: #f97316;">Corpo e Forma</h4>
            <hr style="border-color: #444;">
            
            <ul class="nav flex-column mb-auto">
                <li class="nav-item"><a href="painel.php" class="nav-link active">Início</a></li>
                <li class="nav-item"><a href="alunos.php" class="nav-link">Alunos</a></li>
                <li class="nav-item"><a href="professores.php" class="nav-link">Professores</a></li>
                <li class="nav-item"><a href="exercicios.php" class="nav-link">Exercícios</a></li>
                <li class="nav-item"><a href="fichas.php" class="nav-link">Fichas de Treino</a></li>
                <li class="nav-item"><a href="avaliacoes.php" class="nav-link">Avaliações Físicas</a></li>
                
                <li class="nav-item"><a href="ver_feedbacks.php" class="nav-link">Feedbacks</a></li>
            </ul>
            
            <hr style="border-color: #444;">
            <a href="index.php" class="nav-link text-danger text-center">Sair do Sistema</a>
        </div>
        
        <div class="content p-5 w-100">
            <h2>Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?>!</h2>
            <p style="color: #aaaaaa;">Perfil de acesso: <strong><?php echo ucfirst($_SESSION['tipo_usuario']); ?></strong></p>
            
            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="card card-dash p-4 mb-3 text-white">
                        <h6 class="titulo-card mb-2">Total de Alunos</h6>
                        <h3><?php echo $total_alunos; ?></h3> 
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-dash p-4 mb-3 text-white">
                        <h6 class="titulo-card mb-2">Fichas Vencidas</h6>
                        <h3><?php echo $fichas_vencidas; ?></h3>
                    </div>
                </div>

                <div class="col-md-4">
                    <a href="ver_feedbacks.php" style="text-decoration: none;">
                        <div class="card card-dash p-4 mb-3 text-white">
                            <h6 class="titulo-card mb-2">Feedbacks Recebidos</h6>
                            <h3><?php echo $total_feedbacks; ?></h3>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
