<?php
session_start();
require '../conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: ../index.php");
    exit;
}

$aluno_id = $_SESSION['aluno_id'];

$stmt = $pdo->prepare("SELECT * FROM avaliacoes WHERE aluno_id = :aluno_id ORDER BY data_avaliacao DESC");
$stmt->execute(['aluno_id' => $aluno_id]);
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getClassificacaoIMC($imc) {
    if (empty($imc) || $imc <= 0) return ['texto' => 'Indefinido', 'cor' => '#888888'];
    if ($imc < 18.5) return ['texto' => 'Abaixo do peso', 'cor' => '#0dcaf0'];
    if ($imc < 25.0) return ['texto' => 'Peso normal', 'cor' => '#22c55e'];
    if ($imc < 30.0) return ['texto' => 'Sobrepeso', 'cor' => '#f97316'];
    if ($imc < 35.0) return ['texto' => 'Obesidade Grau I', 'cor' => '#ef4444'];
    if ($imc < 40.0) return ['texto' => 'Obesidade Grau II', 'cor' => '#dc2626'];
    return ['texto' => 'Obesidade Grau III', 'cor' => '#991b1b'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evolução Corporal - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { 
            background-color: #121212; 
            color: #ffffff; 
            padding-bottom: 90px; 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .app-header {
            background-color: #1e1e1e;
            padding: 18px 20px;
            border-bottom: 1px solid #333;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-back {
            color: #f97316;
            font-size: 1.5em;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .text-subtitulo {
            color: #aaaaaa !important;
            font-size: 0.95em;
        }

        .card-avaliacao {
            background: linear-gradient(145deg, #1e1e1e, #181818);
            border: 1px solid #333;
            border-top: 4px solid #0dcaf0;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        }

        .badge-data {
            background-color: #2a2a2a;
            color: #cccccc;
            border: 1px solid #444;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85em;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .medida-box {
            background-color: #252525;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 14px;
            text-align: center;
            height: 100%;
        }

        .medida-box.destaque-imc {
            border-color: #f97316;
            background-color: #221c18;
        }

        .medida-label {
            color: #888888;
            font-size: 0.75em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .medida-valor {
            font-size: 1.3em;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
        }

        .badge-imc-status {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.75em;
            font-weight: 600;
            color: #ffffff;
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
        <a href="painel_aluno.php" class="btn-back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="mb-0 fw-bold" style="color: #ffffff;">Evolução Corporal</h4>
    </div>

    <div class="container mt-4 px-3">
        <p class="text-subtitulo mb-4">Acompanhe seu histórico de medidas:</p>

        <?php if (empty($avaliacoes)): ?>
            <div class="text-center py-5">
                <i class="bi bi-clipboard-x text-muted" style="font-size: 3em;"></i>
                <p class="text-subtitulo mt-3">Você ainda não possui nenhuma avaliação física cadastrada pelo seu professor.</p>
            </div>
        <?php else: ?>

            <?php foreach ($avaliacoes as $av): ?>
                <?php 
                    $data_av = date('d/m/Y', strtotime($av['data_avaliacao']));
                    
                    $peso = floatval($av['peso'] ?? 0);
                    $altura = floatval($av['altura'] ?? 0);
                    
                    $alt_m = ($altura > 3) ? ($altura / 100) : $altura;
                    
                    $imc = 0;
                    if (!empty($av['imc']) && $av['imc'] > 0) {
                        $imc = floatval($av['imc']);
                    } elseif ($peso > 0 && $alt_m > 0) {
                        $imc = $peso / ($alt_m * $alt_m);
                    }

                    $imc_val = ($imc > 0) ? number_format($imc, 1, ',', '.') : '--';
                    $class_imc = getClassificacaoIMC($imc);
                    $gordura_val = !empty($av['percentual_gordura']) ? number_format($av['percentual_gordura'], 1, ',', '.') . '%' : '--';
                ?>
                <div class="card-avaliacao">
                    <div class="mb-3">
                        <span class="badge-data">
                            <i class="bi bi-calendar-event"></i> <?php echo $data_av; ?>
                        </span>
                    </div>

                    <div class="row g-2">
                        <div class="col-6 mb-2">
                            <div class="medida-box">
                                <div class="medida-label">Peso</div>
                                <div class="medida-valor"><?php echo number_format($peso, 2, ',', '.'); ?> kg</div>
                            </div>
                        </div>

                        <div class="col-6 mb-2">
                            <div class="medida-box">
                                <div class="medida-label">Altura</div>
                                <div class="medida-valor"><?php echo number_format($alt_m, 2, ',', '.'); ?> m</div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="medida-box">
                                <div class="medida-label">Gordura</div>
                                <div class="medida-valor"><?php echo $gordura_val; ?></div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="medida-box destaque-imc">
                                <div class="medida-label">IMC</div>
                                <div class="medida-valor" style="color: #f97316;"><?php echo $imc_val; ?></div>
                                <?php if ($imc > 0): ?>
                                    <span class="badge-imc-status" style="background-color: <?php echo $class_imc['cor']; ?>;">
                                        <?php echo $class_imc['texto']; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>

    <div class="bottom-nav">
        <a href="painel_aluno.php" class="nav-item">
            <i class="bi bi-house-door"></i>
            Início
        </a>
        <a href="perfil.php" class="nav-item">
            <i class="bi bi-person"></i>
            Perfil
        </a>
    </div>

</body>
</html>