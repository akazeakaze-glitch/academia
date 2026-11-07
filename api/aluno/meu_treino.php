<?php
session_start();
require_once __DIR__ . '/../conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: ../index.php");
    exit;
}

$aluno_id =$_SESSION['aluno_id'];

// 1. Buscar dados do aluno
$stmt_aluno =$pdo->prepare("SELECT sexo, treino_atual FROM alunos WHERE id = :id");
$stmt_aluno->execute(['id' =>$aluno_id]);
$aluno_dados =$stmt_aluno->fetch(PDO::FETCH_ASSOC);

$sexo_aluno = strtoupper($aluno_dados['sexo'] ?? 'M');

// Validar divisão selecionada (A, B, C ou D)
$treino_atual =$_GET['treino'] ?? $_POST['treino'] ?? $aluno_dados['treino_atual'] ?? 'A';
if (!in_array($treino_atual, ['A', 'B', 'C', 'D'])) {$treino_atual = 'A';
}

// 2. Processar envio do feedback no modal
$mensagem_sucesso = '';$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['finalizar_com_feedback'])) {$comentario = trim($_POST['comentario'] ?? '');$treino_enviado = $_POST['treino'] ?? $treino_atual;

        if (!empty($comentario)) {
            $stmt =$pdo->prepare("INSERT INTO feedback_treino (aluno_id, treino, comentario) VALUES (:aluno_id, :treino, :comentario)");
            if ($stmt->execute(['aluno_id' => $aluno_id, 'treino' =>$treino_enviado, 'comentario' => $comentario])) {$mensagem_sucesso = "Treino concluído e feedback enviado ao professor!";
            } else {
                $mensagem_erro = "Erro ao enviar o feedback. Tente novamente.";
            }
        } else {
            $mensagem_sucesso = "Treino concluído com sucesso!";
        }
    } elseif (isset($_POST['finalizar_sem_feedback'])) {$mensagem_sucesso = "Treino concluído com sucesso!";
    }
}

// 3. Buscar ficha cadastrada no banco
$stmt =$pdo->prepare("SELECT * FROM fichas WHERE aluno_id = :aluno_id ORDER BY id DESC LIMIT 1");
$stmt->execute(['aluno_id' =>$aluno_id]);
$ficha =$stmt->fetch(PDO::FETCH_ASSOC);

$exercicios = [];

if ($ficha) {
    try {
        $sql = "SELECT e.nome, e.grupo_muscular, fe.series, fe.repeticoes, fe.divisao, fe.carga 
                FROM fichas_exercicios fe 
                JOIN exercicios e ON fe.exercicio_id = e.id 
                WHERE fe.ficha_id = :ficha_id AND fe.divisao = :divisao";
        $stmt_ex =$pdo->prepare($sql);$stmt_ex->execute(['ficha_id' => $ficha['id'], 'divisao' =>$treino_atual]);
        $exercicios =$stmt_ex->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {$exercicios = [];
    }
}

// 4. Se não houver exercícios gravados no banco, exibe o treino padrão ABCD (Masculino e Feminino)
if (empty($exercicios)) {$treinos_padrao = [
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
                ['grupo_muscular' => 'BÍCEPS', 'nome' => 'Rosca Alternada com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'ABDÔMEN', 'nome' => 'Abdominal Supra', 'series' => '3', 'repeticoes' => '15-20', 'carga' => '']
            ],
            'C' => [
                ['grupo_muscular' => 'QUADRÍCEPS', 'nome' => 'Agachamento Smith', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'QUADRÍCEPS', 'nome' => 'Leg Press 45°', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'QUADRÍCEPS', 'nome' => 'Cadeira Extensora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'POSTERIOR', 'nome' => 'Mesa Flexora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'PANTURRILHA', 'nome' => 'Panturrilha em Pé', 'series' => '3', 'repeticoes' => '12-15', 'carga' => '']
            ],
            'D' => [
                ['grupo_muscular' => 'OMBROS', 'nome' => 'Desenvolvimento com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'OMBROS', 'nome' => 'Elevação Frontal', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'TRÍCEPS', 'nome' => 'Tríceps Testa', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'BÍCEPS', 'nome' => 'Rosca Martelo', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'CORE', 'nome' => 'Prancha Abdominal', 'series' => '3', 'repeticoes' => '45 seg', 'carga' => '']
            ]
        ],
        'F' => [
            'A' => [
                ['grupo_muscular' => 'QUADRÍCEPS', 'nome' => 'Agachamento Smith', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'QUADRÍCEPS', 'nome' => 'Leg Press 45°', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'QUADRÍCEPS', 'nome' => 'Cadeira Extensora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'GLÚTEOS', 'nome' => 'Coice na Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'PANTURRILHA', 'nome' => 'Panturrilha na Máquina', 'series' => '3', 'repeticoes' => '12-15', 'carga' => '']
            ],
            'B' => [
                ['grupo_muscular' => 'COSTAS', 'nome' => 'Puxada Alta Frontal', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'COSTAS', 'nome' => 'Remada Baixa', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'OMBROS', 'nome' => 'Elevação Lateral Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'BÍCEPS', 'nome' => 'Rosca Direta com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'TRÍCEPS', 'nome' => 'Tríceps com Corda na Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => '']
            ],
            'C' => [
                ['grupo_muscular' => 'GLÚTEOS', 'nome' => 'Elevação Pélvica na Máquina', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'GLÚTEOS', 'nome' => 'Coice na Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'GLÚTEOS', 'nome' => 'Cadeira Abdutora', 'series' => '3', 'repeticoes' => '12-15', 'carga' => ''],
                ['grupo_muscular' => 'POSTERIOR', 'nome' => 'Mesa Flexora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
                ['grupo_muscular' => 'PANTURRILHA', 'nome' => 'Panturrilha no Leg Press', 'series' => '3', 'repeticoes' => '12-15', 'carga' => '']
            ],
            'D' => [
                ['grupo_muscular' => 'ABDÔMEN', 'nome' => 'Abdominal Infra', 'series' => '3', 'repeticoes' => '15-20', 'carga' => ''],
                ['grupo_muscular' => 'CORE', 'nome' => 'Prancha Abdominal', 'series' => '3', 'repeticoes' => '45 seg', 'carga' => ''],
                ['grupo_muscular' => 'CARDIO', 'nome' => 'Esteira / Elíptico', 'series' => '1', 'repeticoes' => '20 min', 'carga' => '']
            ]
        ]
    ];

    $chave_sexo = ($sexo_aluno === 'F') ? 'F' : 'M';
    $exercicios =$treinos_padrao[$chave_sexo][$treino_atual] ?? [];
}

$nome_ficha =$sexo_aluno == 'F' ? 'Adaptação Feminina (Descanso: 1m30s a 2m)' : 'Adaptação Masculina (Descanso: 1m30s a 2m)';
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
        
        /* TÍTULO IGUAL À FOTO 1 */
        .titulo-ficha { color: #f59e0b; font-size: 1.15rem; font-weight: 700; text-align: center; margin-top: 10px; margin-bottom: 6px; }
        .pill-vencimento { background-color: #1c1917; border: 1px solid #78350f; color: #f97316; font-size: 0.75rem; padding: 3px 12px; border-radius: 6px; display: inline-block; }
        .titulo-treino { color: #f97316; font-size: 1.4rem; font-weight: 800; text-align: center; margin-top: 15px; margin-bottom: 20px; }
        
        /* SELETOR DE ABAS ABCD */
        .nav-abcd { display: flex; gap: 8px; justify-content: center; margin-bottom: 20px; }
        .btn-abcd { background-color: #1a1a1a; color: #888; border: 1px solid #333; border-radius: 8px; padding: 6px 16px; font-weight: bold; text-decoration: none; transition: 0.2s; }
        .btn-abcd.active { background-color: #f97316; color: #ffffff; border-color: #f97316; }

        /* CARD
