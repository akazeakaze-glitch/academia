<?php
session_start();
require_once __DIR__ . '/../conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: ../index.php");
    exit;
}

$aluno_nome = $_SESSION['aluno_nome'];
$primeiro_nome = explode(" ", $aluno_nome)[0];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu App - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Ícones do Bootstrap para o menu inferior -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { 
            background-color: #121212; 
            color: #ffffff; 
            padding-bottom: 80px; 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        
        .app-header {
            background-color: #1e1e1e;
            padding: 20px;
            border-bottom: 1px solid #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .text-claro {
            color: #aaaaaa !important;
        }

        .card-acao {
            background: linear-gradient(145deg, #222222, #1a1a1a);
            border: 1px solid #333;
            border-left: 5px solid #f97316;
            border-radius: 16px;
            padding: 30px 20px;
            text-align: center;
            text-decoration: none;
            display: block;
            margin-bottom: 20px;
            color: #ffffff;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        }

        .card-acao:hover, .card-acao:active {
            background: linear-gradient(145deg, #2a2a2a, #222222);
            border-color: #444;
            transform: translateY(-2px);
            color: #ffffff;
        }

        .card-acao.card-evolucao {
            border-left-color: #0dcaf0;
        }

        .card-subtexto {
            color: #b0b0b0 !important;
            font-size: 0.95em;
            margin-bottom: 0;
            font-weight: 400;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #1e1e1e;
            border-top: 1px solid #333;
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            z-index: 1000;
        }

        .nav-item {
            text-align: center;
            color: #888888;
            text-decoration: none;
            font-size: 0.85em;
            width: 50%;
            transition: color 0.2s;
        }

        .nav-item i {
            display: block;
            font-size: 1.5em;
            margin-bottom: 2px;
        }

        .nav-item.active {
            color: #f97316;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="app-header">
        <div>
            <span class="text-claro" style="font-size: 0.9em;">Bem-vindo(a),</span><br>
            <strong class="fs-4" style="color: #ffffff;"><?php echo $primeiro_nome; ?></strong>
        </div>
        <div>
            <a href="logout.php" class="text-danger fs-3" title="Sair"><i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>

    <div class="container mt-4">
        <p class="text-claro mb-4" style="font-size: 1.05em;">O que vamos fazer hoje?</p>

        <a href="meu_treino.php" class="card-acao">
            <h3 class="mb-2" style="font-weight: 700; font-size: 1.4em;">Meu Treino</h3>
            <p class="card-subtexto">Visualizar exercícios do dia</p>
        </a>

        <a href="minhas_avaliacoes.php" class="card-acao card-evolucao">
            <h3 class="mb-2" style="font-weight: 700; font-size: 1.4em;">Evolução Corporal</h3>
            <p class="card-subtexto">Ver peso, altura e IMC</p>
        </a>
    </div>

    <div class="bottom-nav">
        <a href="painel_aluno.php" class="nav-item active">
            <i class="bi bi-house-door-fill"></i>
            Início
        </a>
        <a href="perfil.php" class="nav-item">
            <i class="bi bi-person"></i>
            Perfil
        </a>
    </div>

</body>
</html> 
