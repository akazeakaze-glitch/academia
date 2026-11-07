<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT av.*, al.nome AS nome_aluno 
        FROM avaliacoes av 
        JOIN alunos al ON av.aluno_id = al.id 
        ORDER BY av.data_avaliacao DESC";
$stmt = $pdo->query($sql);
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Avaliações Físicas - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #ffffff; overflow-x: hidden; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; box-shadow: 2px 0 10px rgba(0,0,0,0.5); }
        .nav-link { color: #aaaaaa; padding: 10px 15px; margin-bottom: 5px; border-radius: 8px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background-color: #f97316; color: #ffffff; font-weight: bold; }
        
        .table-custom { background-color: #1e1e1e; border-radius: 10px; overflow: hidden; }
        .table-custom th { color: #f97316; font-weight: 600; text-transform: uppercase; font-size: 0.9em; padding: 15px; background-color: #2a2a2a; border-bottom: none; }
        .table-custom td { color: #ffffff; padding: 15px; border-bottom: 1px solid #333; font-size: 0.95em; background-color: #1e1e1e; }
        
        .texto-claro { color: #cccccc !important; }
        .texto-destaque { color: #ffffff !important; font-weight: 500; }
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
                <li class="nav-item"><a href="ver_feedbacks.php" class="nav-link">Feedbacks</a></li>
            </ul>
            <hr style="border-color: #444;">
            <a href="index.php" class="nav-link text-danger text-center">Sair do Sistema</a>
        </div>
        
        <div class="content p-5 w-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Avaliações e Anamnese</h2>
                
                <a href="cadastrar_avaliacao.php" class="btn" style="background-color: #f97316; color: white; font-weight: bold; font-size: 1.1em; padding: 10px 20px;">
                    + Nova Avaliação
                </a>
            </div>
            
            <div class="table-responsive table-custom">
                <table class="table table-borderless align-middle text-center mb-0">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Aluno</th>
                            <th>Peso / Altura</th>
                            <th>IMC</th>
                            <th>Limitações / Alertas</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($avaliacoes as $av): 
                            $peso = $av['peso'];
                            $altura = $av['altura'];
                            $imc = 0;
                            $imc_texto = "N/A";
                            $cor_imc = "texto-claro";

                            if ($peso > 0 && $altura > 0) {
                                $imc = $peso / ($altura * $altura);
                                $imc_formatado = number_format($imc, 1, ',', '.');
                                
                                // Usando cores brilhantes para combinar com o Dark Mode
                                if ($imc < 18.5) { $imc_texto = "$imc_formatado (Abaixo)"; $cor_imc = "text-info"; } 
                                elseif ($imc < 24.9) { $imc_texto = "$imc_formatado (Normal)"; $cor_imc = "text-success"; } 
                                elseif ($imc < 29.9) { $imc_texto = "$imc_formatado (Sobrepeso)"; $cor_imc = "text-warning"; } 
                                else { $imc_texto = "$imc_formatado (Obesidade)"; $cor_imc = "text-danger"; } 
                            }
                        ?>
                        <tr>
                            <td style="color: #aaaaaa;"><?php echo date('d/m/Y', strtotime($av['data_avaliacao'])); ?></td>
                            <td class="text-start ps-4 texto-destaque"><?php echo $av['nome_aluno']; ?></td>
                            <td class="texto-claro"><?php echo $peso; ?>kg / <?php echo $altura; ?>m</td>
                            <td class="<?php echo $cor_imc; ?> fw-bold"><?php echo $imc_texto; ?></td>
                            <td class="text-start">
                                <?php if (!empty($av['limitacoes_fisicas']) && $av['limitacoes_fisicas'] != 'Nenhuma'): ?>
                                    <span class="badge bg-danger">Atenção</span> <span class="texto-claro"><?php echo $av['limitacoes_fisicas']; ?></span>
                                <?php else: ?>
                                    <span class="texto-claro" style="font-style: italic;">Nenhuma relatada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="ver_avaliacao.php?id=<?php echo $av['id']; ?>" class="btn btn-sm btn-outline-info">Ver Detalhes</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($avaliacoes) == 0): ?>
                        <tr>
                            <td colspan="6" class="text-center p-4 texto-claro">Nenhuma avaliação registrada ainda.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
