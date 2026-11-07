<?php
session_start();
require_once __DIR__ . '/../conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: ../index.php");
    exit;
}

$aluno_id = $_SESSION['aluno_id'];

// 1. Consulta em ordem cronológica (ASC) para alimentar o gráfico
$stmt_chart = $pdo->prepare("SELECT data_avaliacao, peso, cintura FROM avaliacoes WHERE aluno_id = :aluno_id ORDER BY data_avaliacao ASC");
$stmt_chart->execute(['aluno_id' => $aluno_id]);
$avaliacoes_chart = $stmt_chart->fetchAll(PDO::FETCH_ASSOC);

// Preparar vetores para o Chart.js
$datas_label = [];
$pesos_data = [];
$cinturas_data = [];

foreach ($avaliacoes_chart as $av) {
    $datas_label[] = date('d/m/Y', strtotime($av['data_avaliacao']));
    $pesos_data[] = (float)$av['peso'];
    $cinturas_data[] = !empty($av['cintura']) ? (float)$av['cintura'] : null;
}

// 2. Consulta em ordem decrescente (DESC) para listar os cartões de histórico
$stmt = $pdo->prepare("SELECT * FROM avaliacoes WHERE aluno_id = :aluno_id ORDER BY data_avaliacao DESC");
$stmt->execute(['aluno_id' => $aluno_id]);
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Avaliações - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- CHART.JS VIA CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { background-color: #121212; color: #ffffff; font-family: 'Segoe UI', sans-serif; padding-bottom: 90px; }
        .card-dark { background-color: #1a1a1a; border: 1px solid #333; border-radius: 12px; }
        .badge-orange { background-color: #f97316; color: #fff; font-weight: bold; }
        .metric-box { background-color: #242424; border: 1px solid #333; border-radius: 8px; padding: 10px; text-align: center; }
        .metric-label { font-size: 0.75rem; color: #aaa; text-transform: uppercase; }
        .metric-value { font-size: 1.1rem; font-weight: bold; color: #fff; }
    </style>
</head>
<body class="py-3">

    <div class="container">
        
        <!-- CABEÇALHO -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary">
            <h4 class="mb-0">Histórico & Evolução</h4>
            <a href="painel_aluno.php" class="btn text-white btn-sm" style="background-color: #252525; border: 1px solid #444; border-radius: 8px;">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <?php if (empty($avaliacoes)): ?>
            <div class="alert alert-dark text-center border-secondary mt-4" role="alert" style="background-color: #1a1a1a; color: #aaa;">
                <i class="bi bi-journal-x fs-2 d-block mb-2 text-muted"></i>
                Ainda não tem nenhuma avaliação física registrada.
            </div>
        <?php else: ?>
            <div class="card card-dark mb-4">
                <div class="card-header" style="background-color: #252525; border-bottom: 1px solid #333; color: #fff; border-radius: 12px 12px 0 0;">
                    <strong><i class="bi bi-graph-up-arrow me-2" style="color: #f97316;"></i> Evolução Corporal</strong>
                </div>
                <div class="card-body" style="position: relative; height: 260px;">
                    <canvas id="graficoEvolucao"></canvas>
                </div>
            </div>

            <!-- HISTÓRICO DE AVALIAÇÕES EM CARDS -->
            <h5 class="mb-3" style="color: #aaa; font-size: 1rem; text-transform: uppercase;">Histórico Detalhado</h5>

            <?php foreach ($avaliacoes as $av): ?>
                <?php 
                    $imc = null;
                    if (!empty($av['altura']) && $av['altura'] > 0) {
                        $imc = number_format($av['peso'] / ($av['altura'] * $av['altura']), 1);
                    }
                ?>
                <div class="card card-dark mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #252525; border-bottom: 1px solid #333; color: #fff; border-radius: 12px 12px 0 0;">
                        <span><i class="bi bi-calendar-event me-2 text-warning"></i> <strong><?= date('d/m/Y', strtotime($av['data_avaliacao'])) ?></strong></span>
                        <span class="badge badge-orange"><?= $av['peso'] ?> kg</span>
                    </div>
                    <div class="card-body">
                        
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="metric-box">
                                    <div class="metric-label">Peso</div>
                                    <div class="metric-value" style="color: #f97316;"><?= $av['peso'] ?> kg</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="metric-box">
                                    <div class="metric-label">Altura</div>
                                    <div class="metric-value"><?= !empty($av['altura']) ? $av['altura'] . ' m' : '-' ?></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="metric-box">
                                    <div class="metric-label">IMC</div>
                                    <div class="metric-value"><?= $imc ? $imc : '-' ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- MEDIDAS CORPORAIS / PERIMETRIA -->
                        <h6 class="mt-3 mb-2" style="color: #aaa; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            Circunferências (cm)
                        </h6>

                        <div class="row g-2">
                            <div class="col-6 col-md-3">
                                <div class="metric-box">
                                    <div class="metric-label">Cintura</div>
                                    <div class="metric-value"><?= !empty($av['cintura']) ? $av['cintura'] . ' cm' : '-' ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="metric-box">
                                    <div class="metric-label">Abdômen</div>
                                    <div class="metric-value"><?= !empty($av['abdomen']) ? $av['abdomen'] . ' cm' : '-' ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="metric-box">
                                    <div class="metric-label">Quadril</div>
                                    <div class="metric-value"><?= !empty($av['quadril']) ? $av['quadril'] . ' cm' : '-' ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="metric-box">
                                    <div class="metric-label">Tórax</div>
                                    <div class="metric-value"><?= !empty($av['torax']) ? $av['torax'] . ' cm' : '-' ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="metric-box">
                                    <div class="metric-label">Braço Dir.</div>
                                    <div class="metric-value"><?= !empty($av['braco_direito']) ? $av['braco_direito'] . ' cm' : '-' ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="metric-box">
                                    <div class="metric-label">Braço Esq.</div>
                                    <div class="metric-value"><?= !empty($av['braco_esquerdo']) ? $av['braco_esquerdo'] . ' cm' : '-' ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="metric-box">
                                    <div class="metric-label">Coxa Dir.</div>
                                    <div class="metric-value"><?= !empty($av['coxa_direita']) ? $av['coxa_direita'] . ' cm' : '-' ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="metric-box">
                                    <div class="metric-label">Coxa Esq.</div>
                                    <div class="metric-value"><?= !empty($av['coxa_esquerda']) ? $av['coxa_esquerda'] . ' cm' : '-' ?></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>

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

    <?php if (!empty($avaliacoes)): ?>
    <script>
        const ctx = document.getElementById('graficoEvolucao').getContext('2d');
        const graficoEvolucao = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($datas_label) ?>,
                datasets: [
                    {
                        label: 'Peso (kg)',
                        data: <?= json_encode($pesos_data) ?>,
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.15)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: '#f97316'
                    },
                    {
                        label: 'Cintura (cm)',
                        data: <?= json_encode($cinturas_data) ?>,
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6, 182, 212, 0.15)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: '#06b6d4'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#ffffff' }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#aaa' },
                        grid: { color: '#333' }
                    },
                    y: {
                        ticks: { color: '#aaa' },
                        grid: { color: '#333' }
                    }
                }
            }
        });
    </script>
    <?php endif; ?>

</body>
</html>
