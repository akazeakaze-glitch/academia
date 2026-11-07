<?php
session_start();
require 'conexao.php'; 

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM alunos ORDER BY nome ASC");
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alunos - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #ffffff; overflow-x: hidden; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; box-shadow: 2px 0 10px rgba(0,0,0,0.5); }
        .nav-link { color: #aaaaaa; padding: 10px 15px; margin-bottom: 5px; border-radius: 8px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background-color: #f97316; color: #ffffff; font-weight: bold; }
        
        .table-custom { background-color: #1e1e1e; border-radius: 10px; overflow: hidden; }
        .table-custom th { color: #f97316; font-weight: 600; text-transform: uppercase; font-size: 0.9em; padding: 15px; background-color: #2a2a2a; border-bottom: none; }
        .table-custom td { color: #ffffff; padding: 15px; border-bottom: 1px solid #333; font-size: 0.95em; background-color: #1e1e1e;}
    </style>
</head>
<body>

    <div class="d-flex">
        <div class="sidebar p-3 d-flex flex-column">
            <h4 class="text-center mt-3 mb-4" style="color: #f97316;">Corpo e Forma</h4>
            <hr style="border-color: #444;">
            <ul class="nav flex-column mb-auto">
                <li class="nav-item"><a href="painel.php" class="nav-link">Início</a></li>
                <li class="nav-item"><a href="alunos.php" class="nav-link active">Alunos</a></li>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Gerenciar Alunos</h2>
                <a href="cadastrar_aluno.php" class="btn" style="background-color: #f97316; color: white; font-weight: bold;">+ Novo Aluno</a>
            </div>
            
            <div class="table-responsive table-custom">
                <table class="table table-borderless mb-0 text-center align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Objetivo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alunos as $aluno): ?>
                        <tr>
                            <td style="color: #aaaaaa;"><?php echo $aluno['id']; ?></td>
                            <td class="text-start fw-bold"><?php echo $aluno['nome']; ?></td>
                            <td> <?php 
                                  $tel = preg_replace('/[^0-9]/', '', $aluno['telefone']); 
                                 if (strlen($tel) == 11) {
                                echo preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $tel);
                                 } elseif (strlen($tel) == 10) { 
                                echo preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $tel);
                                 } else {
                              echo htmlspecialchars($aluno['telefone']);
                                 }
                                 ?>
                            </td>
                            <td><?php echo $aluno['objetivo']; ?></td>
                            <td>
                                <a href="editar_aluno.php?id=<?php echo $aluno['id']; ?>" class="btn btn-sm btn-outline-light">Editar</a>
                                <a href="excluir_aluno.php?id=<?php echo $aluno['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Tem certeza que deseja excluir o aluno <?php echo $aluno['nome']; ?>? Todas as fichas e avaliações dele também serão apagadas!');">
                                    Excluir
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($alunos) == 0): ?>
                        <tr>
                            <td colspan="5" class="text-center p-4">Nenhum aluno cadastrado ainda.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
