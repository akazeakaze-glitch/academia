<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: fichas.php");
    exit;
}

$ficha_id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['adicionar'])) {
    $exercicio_id = $_POST['exercicio_id'];
    $divisao = strtoupper($_POST['divisao']); 
    $series = $_POST['series'];
    $repeticoes = $_POST['repeticoes'];
    $carga = $_POST['carga'];

    $sql_insert = "INSERT INTO ficha_exercicios (ficha_id, exercicio_id, divisao, series, repeticoes, carga) 
                   VALUES (:ficha_id, :exercicio_id, :divisao, :series, :repeticoes, :carga)";
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute([
        'ficha_id' => $ficha_id,
        'exercicio_id' => $exercicio_id,
        'divisao' => $divisao,
        'series' => $series,
        'repeticoes' => $repeticoes,
        'carga' => $carga
    ]);

    header("Location: ver_treino.php?id=" . $ficha_id);
    exit;
}

$sql_ficha = "SELECT f.*, a.nome AS nome_aluno, a.objetivo FROM fichas f JOIN alunos a ON f.aluno_id = a.id WHERE f.id = :id";
$stmt = $pdo->prepare($sql_ficha);
$stmt->execute(['id' => $ficha_id]);
$ficha = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt_cat = $pdo->query("SELECT id, nome, grupo_muscular FROM exercicios ORDER BY grupo_muscular, nome");
$catalogo_exercicios = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

$sql_exercicios = "SELECT fe.*, e.nome AS nome_exercicio, e.grupo_muscular, e.equipamento 
                   FROM ficha_exercicios fe JOIN exercicios e ON fe.exercicio_id = e.id 
                   WHERE fe.ficha_id = :ficha_id ORDER BY fe.divisao ASC, e.grupo_muscular ASC";
$stmt_ex = $pdo->prepare($sql_exercicios);
$stmt_ex->execute(['ficha_id' => $ficha_id]);
$exercicios = $stmt_ex->fetchAll(PDO::FETCH_ASSOC);

$treinos = [];
foreach ($exercicios as $ex) {
    $divisao = $ex['divisao'];
    $treinos[$divisao][] = $ex; 
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Treino - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .text-muted {
               color: #b0b0b0 !important;
            }
        body { background-color: #121212; color: #ffffff; overflow-x: hidden; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; }
        .nav-link { color: #aaaaaa; padding: 10px 15px; margin-bottom: 5px; border-radius: 8px; }
        .nav-link:hover, .nav-link.active { background-color: #f97316; color: #ffffff; }
        .form-control, .form-select { background-color: #2a2a2a; border: 1px solid #444; color: white; }
        .form-control:focus, .form-select:focus { background-color: #2a2a2a; border-color: #f97316; color: white; box-shadow: none; }
        .card-treino { background-color: #1e1e1e; border: 1px solid #333; border-top: 4px solid #f97316; border-radius: 10px; }
        .badge-grupo { background-color: #333; color: #f97316; border: 1px solid #f97316; }
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
                <li class="nav-item"><a href="exercicios.php" class="nav-link">Exercícios</a></li>
                <li class="nav-item"><a href="fichas.php" class="nav-link active">Fichas de Treino</a></li>
                <li class="nav-item"><a href="avaliacoes.php" class="nav-link">Avaliações Físicas</a></li>
            </ul>
        </div>
        
        <div class="content p-5 w-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">Treino de <?php echo $ficha['nome_aluno']; ?></h2>
                    <p class="text-muted">Ficha: <?php echo $ficha['nome_ficha']; ?> | Válida até: <?php echo date('d/m/Y', strtotime($ficha['data_vencimento'])); ?></p>
                </div>
                <a href="fichas.php" class="btn btn-outline-light">Voltar</a>
            </div>

            <div class="card p-3 mb-4" style="background-color: #1e1e1e; border: 1px dashed #f97316;">
                <form action="ver_treino.php?id=<?php echo $ficha_id; ?>" method="POST" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label" style="color: #ccc; font-size: 0.9em;">Exercício do Catálogo</label>
                        <select name="exercicio_id" class="form-select form-select-sm" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($catalogo_exercicios as $cat_ex): ?>
                                <option value="<?php echo $cat_ex['id']; ?>">
                                    <?php echo $cat_ex['nome']; ?> (<?php echo $cat_ex['grupo_muscular']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="color: #ccc; font-size: 0.9em;">Divisão (A, B, C...)</label>
                        <input type="text" name="divisao" class="form-control form-control-sm" maxlength="1" required placeholder="Ex: A">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="color: #ccc; font-size: 0.9em;">Séries</label>
                        <input type="text" name="series" class="form-control form-control-sm" placeholder="Ex: 4" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="color: #ccc; font-size: 0.9em;">Repetições</label>
                        <input type="text" name="repeticoes" class="form-control form-control-sm" placeholder="Ex: 10 a 12" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="color: #ccc; font-size: 0.9em;">Carga (Opcional)</label>
                        <input type="text" name="carga" class="form-control form-control-sm" placeholder="Ex: 20kg">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" name="adicionar" class="btn btn-sm w-100" style="background-color: #f97316; color: white; font-weight: bold;">+</button>
                    </div>
                </form>
            </div>
            
            <div class="row">
                <?php if(empty($treinos)): ?>
                    <p class="text-center text-muted mt-4">Nenhum exercício cadastrado nesta ficha ainda.</p>
                <?php endif; ?>

                <?php foreach ($treinos as $divisao => $lista_exercicios): ?>
                <div class="col-md-6 mb-4">
                    <div class="card card-treino p-4 h-100">
                        <h4 class="mb-3" style="color: #f97316;">Treino <?php echo $divisao; ?></h4>
                        
                        <div class="table-responsive">
                            <table class="table table-dark table-borderless table-sm align-middle">
                                <thead style="color: #888;">
                                    <tr>
                                        <th>Exercício</th>
                                        <th class="text-center">Séries</th>
                                        <th class="text-center">Reps</th>
                                        <th class="text-center">Carga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lista_exercicios as $ex): ?>
                                    <tr style="border-bottom: 1px solid #333;">
                                        <td class="py-2">
                                            <span class="d-block fw-bold"><?php echo $ex['nome_exercicio']; ?></span>
                                            <span class="badge badge-grupo" style="font-size: 0.7em;"><?php echo $ex['grupo_muscular']; ?></span>
                                        </td>
                                        <td class="text-center py-2"><?php echo $ex['series']; ?></td>
                                        <td class="text-center py-2"><?php echo $ex['repeticoes']; ?></td>
                                        <td class="text-center py-2 text-warning"><?php echo $ex['carga']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
        </div>
    </div>

</body>
</html>