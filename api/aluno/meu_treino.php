<?php
session_start();
require_once __DIR__ . '/../conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: ../index.php");
    exit;
}

$aluno_id = $_SESSION['aluno_id'];

// --- LÓGICA DO FEEDBACK (ITEM A) ---
$mensagem_sucesso = '';
$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'])) {
    $comentario = trim($_POST['comentario']);

    if (!empty($comentario) && $aluno_id) {
        $stmt = $pdo->prepare("INSERT INTO feedback_treino (aluno_id, comentario) VALUES (:aluno_id, :comentario)");
        if ($stmt->execute(['aluno_id' => $aluno_id, 'comentario' => $comentario])) {
            $mensagem_sucesso = "Seu feedback foi enviado ao professor com sucesso!";
        } else {
            $mensagem_erro = "Erro ao enviar o feedback. Tente novamente.";
        }
    }
}

$aluno_id = $_SESSION['aluno_id'];

$stmt_aluno = $pdo->prepare("SELECT sexo, treino_atual FROM alunos WHERE id = :id");
$stmt_aluno->execute(['id' => $aluno_id]);
$aluno_dados = $stmt_aluno->fetch(PDO::FETCH_ASSOC);
$sexo_aluno = $aluno_dados['sexo'] ?? 'M';
$treino_atual = $aluno_dados['treino_atual'] ?? 'A'; 

$stmt = $pdo->prepare("SELECT * FROM fichas WHERE aluno_id = :aluno_id ORDER BY id DESC LIMIT 1");
$stmt->execute(['aluno_id' => $aluno_id]);
$ficha = $stmt->fetch(PDO::FETCH_ASSOC);

$exercicios = [];
$erro_banco = false;
$mensagem_erro_sql = "";

if ($ficha) {
    try {
        $sql = "SELECT e.nome, e.grupo_muscular, fe.series, fe.repeticoes, fe.divisao, fe.carga 
                FROM fichas_exercicios fe 
                JOIN exercicios e ON fe.exercicio_id = e.id 
                WHERE fe.ficha_id = :ficha_id";
        $stmt_ex = $pdo->prepare($sql);
        $stmt_ex->execute(['ficha_id' => $ficha['id']]);
        $exercicios = $stmt_ex->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $erro_banco = true;
        $mensagem_erro_sql = $e->getMessage();
    }
}

if (!$ficha || count($exercicios) == 0) {
    $ficha = [
        'nome_ficha' => $sexo_aluno == 'F' ? 'Adaptação Feminina (Descanso: 1m30s a 2m)' : 'Adaptação Masculina (Descanso: 1m30s a 2m)',
        'data_vencimento' => date('Y-m-d', strtotime('+30 days'))
    ];
    
    if ($sexo_aluno == 'F') {
        $exercicios = [
            ['divisao' => 'A', 'grupo_muscular' => 'Quadríceps', 'nome' => 'Agachamento Smith', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'A', 'grupo_muscular' => 'Quadríceps', 'nome' => 'Leg Press 45°', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'A', 'grupo_muscular' => 'Quadríceps', 'nome' => 'Cadeira Extensora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'A', 'grupo_muscular' => 'Glúteos', 'nome' => 'Coice na Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'A', 'grupo_muscular' => 'Panturrilha', 'nome' => 'Panturrilha na Máquina', 'series' => '3', 'repeticoes' => '12-15', 'carga' => ''],
            ['divisao' => 'B', 'grupo_muscular' => 'Costas', 'nome' => 'Puxada Alta Frontal', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'B', 'grupo_muscular' => 'Costas', 'nome' => 'Remada Baixa', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'B', 'grupo_muscular' => 'Ombros', 'nome' => 'Elevação Lateral', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'B', 'grupo_muscular' => 'Bíceps', 'nome' => 'Rosca Direta com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'B', 'grupo_muscular' => 'Tríceps', 'nome' => 'Tríceps com Corda na Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'C', 'grupo_muscular' => 'Glúteos', 'nome' => 'Elevação Pélvica na Máquina', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'C', 'grupo_muscular' => 'Glúteos', 'nome' => 'Coice na Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'C', 'grupo_muscular' => 'Posterior', 'nome' => 'Mesa Flexora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'C', 'grupo_muscular' => 'Posterior', 'nome' => 'Stiff com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'C', 'grupo_muscular' => 'Glúteos', 'nome' => 'Cadeira Abdutora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'C', 'grupo_muscular' => 'Panturrilha', 'nome' => 'Panturrilha na Máquina', 'series' => '3', 'repeticoes' => '12-15', 'carga' => ''],
            ['divisao' => 'D', 'grupo_muscular' => 'Costas', 'nome' => 'Puxada Alta Neutra', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'D', 'grupo_muscular' => 'Peito', 'nome' => 'Supino Máquina', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'D', 'grupo_muscular' => 'Ombros', 'nome' => 'Elevação Lateral', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'D', 'grupo_muscular' => 'Tríceps', 'nome' => 'Tríceps Francês na Máquina', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'D', 'grupo_muscular' => 'Bíceps', 'nome' => 'Rosca Direta com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => '']
        ];
    } else {
        $exercicios = [
            ['divisao' => 'A', 'grupo_muscular' => 'Peito', 'nome' => 'Supino Inclinado com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'A', 'grupo_muscular' => 'Peito', 'nome' => 'Supino Reto Máquina', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'A', 'grupo_muscular' => 'Peito', 'nome' => 'Crucifixo na Máquina', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'A', 'grupo_muscular' => 'Ombros', 'nome' => 'Desenvolvimento na Máquina', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'A', 'grupo_muscular' => 'Ombros', 'nome' => 'Elevação Lateral Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'A', 'grupo_muscular' => 'Tríceps', 'nome' => 'Tríceps com Corda na Polia', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'B', 'grupo_muscular' => 'Costas', 'nome' => 'Puxada Alta Frontal', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'B', 'grupo_muscular' => 'Costas', 'nome' => 'T-Bar', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'B', 'grupo_muscular' => 'Costas', 'nome' => 'Remada Baixa na Máquina', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'B', 'grupo_muscular' => 'Bíceps', 'nome' => 'Rosca Direta na Barra W', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'B', 'grupo_muscular' => 'Bíceps', 'nome' => 'Rosca Martelo com Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'C', 'grupo_muscular' => 'Quadríceps', 'nome' => 'Agachamento Smith', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'C', 'grupo_muscular' => 'Quadríceps', 'nome' => 'Leg Press 45°', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'C', 'grupo_muscular' => 'Quadríceps', 'nome' => 'Cadeira Extensora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'C', 'grupo_muscular' => 'Posterior', 'nome' => 'Mesa Flexora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'C', 'grupo_muscular' => 'Posterior', 'nome' => 'Cadeira Flexora', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'C', 'grupo_muscular' => 'Panturrilha', 'nome' => 'Panturrilha na Máquina', 'series' => '3', 'repeticoes' => '12-15', 'carga' => ''],
            ['divisao' => 'D', 'grupo_muscular' => 'Peito', 'nome' => 'Supino Inclinado na Máquina', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'D', 'grupo_muscular' => 'Costas', 'nome' => 'Puxada Alta com Pegada Neutra', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'D', 'grupo_muscular' => 'Ombros', 'nome' => 'Elevação Lateral Halteres', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'D', 'grupo_muscular' => 'Bíceps', 'nome' => 'Rosca Scott', 'series' => '3', 'repeticoes' => '10-12', 'carga' => ''],
            ['divisao' => 'D', 'grupo_muscular' => 'Tríceps', 'nome' => 'Tríceps Francês na Máquina', 'series' => '3', 'repeticoes' => '10-12', 'carga' => '']
        ];
    }
}

$letras_disponiveis = array_unique(array_column($exercicios, 'divisao'));
sort($letras_disponiveis); 

if (!in_array($treino_atual, $letras_disponiveis)) {
    $treino_atual = $letras_disponiveis[0] ?? 'A';
}

$index_atual = array_search($treino_atual, $letras_disponiveis);
$index_proximo = ($index_atual + 1) % count($letras_disponiveis);
$proximo_treino = $letras_disponiveis[$index_proximo];

$grupos_proximo = [];
foreach ($exercicios as $ex) {
    if ($ex['divisao'] == $proximo_treino) {
        $grupos_proximo[] = $ex['grupo_muscular'];
    }
}
$descricao_proximo = implode(', ', array_unique($grupos_proximo));

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalizar'])) {
    $stmt_up = $pdo->prepare("UPDATE alunos SET treino_atual = :prox WHERE id = :id");
    $stmt_up->execute(['prox' => $proximo_treino, 'id' => $aluno_id]);
    
    $_SESSION['sucesso_treino'] = "Parabéns! Treino $treino_atual concluído. O seu próximo será o Treino $proximo_treino ($descricao_proximo).";
    header("Location: meu_treino.php");
    exit;
}

$exercicios_hoje = array_filter($exercicios, function($ex) use ($treino_atual) {
    return $ex['divisao'] == $treino_atual;
});
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Treino - Corpo e Forma</title>
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
            text-align: center; 
            position: sticky; 
            top: 0; 
            z-index: 1000; 
        }
        
        .divisao-destaque { 
            color: #f97316; 
            font-size: 1.5em; 
            font-weight: bold; 
            text-align: center; 
            margin: 20px 0; 
        }
        
        .card-exercicio { 
            background-color: #1e1e1e; 
            border-radius: 12px; 
            padding: 15px; 
            margin-bottom: 15px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-left: 4px solid #f97316; 
        }
        
        .grupo-muscular { 
            font-size: 0.8em; 
            color: #f97316; 
            text-transform: uppercase; 
            font-weight: bold; 
            margin-bottom: 3px; 
        }
        
        .nome-exercicio { 
            font-size: 1.1em; 
            font-weight: 600; 
            margin-bottom: 5px; 
        }
        
        .series-reps { 
            color: #aaaaaa; 
            font-size: 0.9em; 
        }
        
        .check-treino { 
            width: 35px; 
            height: 35px; 
            background-color: #2a2a2a; 
            border: 2px solid #555; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            cursor: pointer; 
            transition: 0.3s; 
        }
        
        .check-treino input { display: none; }
        .check-treino input:checked + i { color: #22c55e; opacity: 1; }
        .check-treino i { color: transparent; font-size: 1.5em; opacity: 0; transition: 0.2s; }
        
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
        <h5 class="mb-1 text-warning fw-bold"><?php echo htmlspecialchars($ficha['nome_ficha']); ?></h5>
        
        <div class="mt-2">
            <span style="background-color: #2a2a2a; color: #f97316; border: 1px solid #444; padding: 5px 12px; border-radius: 8px; font-size: 0.85em; display: inline-block;">
                <i class="bi bi-calendar-check"></i> Vence a: <?php echo date('d/m/Y', strtotime($ficha['data_vencimento'])); ?>
            </span>
        </div>
    </div>

    <div class="container mt-3">
        
        <?php if (isset($_SESSION['sucesso_treino'])): ?>
            <div class="alert alert-success text-center fw-bold" style="background-color: #22c55e; color: white; border: none; border-radius: 12px;">
                <i class="bi bi-trophy-fill"></i> <?php echo $_SESSION['sucesso_treino']; ?>
            </div>
            <?php unset($_SESSION['sucesso_treino']); ?>
        <?php endif; ?>

        <?php if ($erro_banco): ?>
            <div class="alert mt-4" style="background-color: #ef4444; color: white;">
                Erro: <?php echo $mensagem_erro_sql; ?>
            </div>
        <?php else: ?>
            
            <div class="divisao-destaque">Treino <?php echo $treino_atual; ?></div>
            
            <?php foreach ($exercicios_hoje as $ex): ?>
                <div class="card-exercicio">
                    <div>
                        <div class="grupo-muscular"><?php echo $ex['grupo_muscular']; ?></div>
                        <div class="nome-exercicio"><?php echo $ex['nome']; ?></div>
                        <div class="series-reps">
                            <i class="bi bi-arrow-repeat"></i> <?php echo $ex['series']; ?> x <?php echo $ex['repeticoes']; ?> 
                            <?php if(!empty($ex['carga'])): ?>
                                | <i class="bi bi-moisture"></i> <?php echo $ex['carga']; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <label class="check-treino">
                            <input type="checkbox">
                            <i class="bi bi-check-circle-fill"></i>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <form action="meu_treino.php" method="POST">
                <button type="submit" name="finalizar" class="btn w-100 mt-3 p-3 mb-4" style="background-color: #f97316; color: white; font-weight: bold; border-radius: 12px;">
                    <i class="bi bi-send-check"></i> Finalizar Treino de Hoje
                </button>
            </form>
            
        <?php endif; ?>

    </div>
<div class="card mt-4 mb-4" style="background-color: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 12px;">
        <div class="card-header" style="background-color: #252525; border-bottom: 1px solid #333; color: #fff; border-radius: 12px 12px 0 0;">
            <h5 class="card-title mb-0" style="font-size: 1.1rem;"><i class="bi bi-chat-left-text"></i> Enviar Feedback ao Professor</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="comentario" class="form-label" style="color: #ccc; font-weight: 500;">O que achou do treino de hoje?</label>
                    <textarea class="form-control" id="comentario" name="comentario" rows="3" placeholder="Ex: Achei o treino muito pesado nas pernas, gostaria de adicionar mais exercícios..." required style="background-color: #121212; color: #fff; border: 1px solid #444; border-radius: 8px;"></textarea>
                </div>
                <button type="submit" class="btn w-100" style="background-color: #f97316; color: #fff; font-weight: bold; border-radius: 8px; padding: 10px;">Enviar Mensagem</button>
            </form>
        </div>
    </div>

<div style="position: fixed; bottom: 0; left: 0; right: 0; width: 100%; background-color: #1a1a1a; border-top: 1px solid #333; display: flex; justify-content: space-around; align-items: center; padding: 10px 0; z-index: 1000;">
    <a href="painel_aluno.php" style="color: #ffffff; text-decoration: none; text-align: center; flex: 1; display: flex; flex-direction: column; align-items: center; font-size: 0.8rem; font-weight: 500;">
        <i class="bi bi-house-door" style="font-size: 1.4rem; margin-bottom: 3px; color: #f97316;"></i>
        <span>Início</span>
    </a>
    <a href="perfil.php" style="color: #ffffff; text-decoration: none; text-align: center; flex: 1; display: flex; flex-direction: column; align-items: center; font-size: 0.8rem; font-weight: 500;">
        <i class="bi bi-person" style="font-size: 1.4rem; margin-bottom: 3px; color: #f97316;"></i>
        <span>Perfil</span>
    </a>
</div>

</body>
</html>
