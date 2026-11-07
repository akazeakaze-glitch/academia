<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM professores ORDER BY nome ASC");
$professores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Professores - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #ffffff; overflow-x: hidden; }
        .sidebar { background-color: #1e1e1e; min-height: 100vh; width: 250px; box-shadow: 2px 0 10px rgba(0,0,0,0.5); }
        .nav-link { color: #aaaaaa; padding: 10px 15px; margin-bottom: 5px; border-radius: 8px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background-color: #f97316; color: #ffffff; font-weight: bold; }
        
        .card-prof { background-color: #1e1e1e; border: 1px solid #333; border-radius: 15px; overflow: hidden; transition: transform 0.2s; }
        .card-prof:hover { transform: translateY(-5px); border-color: #f97316; }
        
        .foto-prof { width: 100%; height: 380px; object-fit: cover; background-color: #2a2a2a; }
        
        .texto-claro { color: #e0e0e0; font-size: 0.95em; }
        
        .btn-whatsapp { background-color: #25D366; color: white; font-weight: bold; border: none; }
        .btn-whatsapp:hover { background-color: #128C7E; color: white; }
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
                <li class="nav-item"><a href="professores.php" class="nav-link active">Professores</a></li>
                <li class="nav-item"><a href="exercicios.php" class="nav-link">Exercícios</a></li>
                <li class="nav-item"><a href="fichas.php" class="nav-link">Fichas de Treino</a></li>
                <li class="nav-item"><a href="avaliacoes.php" class="nav-link">Avaliações Físicas</a></li>
                <li class="nav-item"><a href="ver_feedbacks.php" class="nav-link">Feedbacks</a></li>
            </ul>
            <hr style="border-color: #444;">
            <a href="index.php" class="nav-link text-danger text-center">Sair do Sistema</a>
        </div>
        
        <div class="content p-5 w-100">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2>Nossa Equipe de Professores</h2>
                <a href="cadastrar_professor.php" class="btn" style="background-color: #f97316; color: white; font-weight: bold;">+ Novo Professor</a>
            </div>
            
            <div class="row">
                <?php foreach ($professores as $prof): 
                    $numero_limpo = preg_replace('/[^0-9]/', '', $prof['telefone']);
                ?>
                <div class="col-md-3 mb-4">
                    <div class="card card-prof text-center h-100">
                        <img src="https://via.placeholder.com/300x400/2a2a2a/f97316?text=Foto" class="foto-prof" alt="Foto">
                        
                        <div class="card-body p-4">
                            <h5 class="card-title text-white fw-bold"><?php echo $prof['nome']; ?></h5>
                            <p class="texto-claro mb-3"><?php echo $prof['especialidade']; ?></p>
                            
                            <a href="https://wa.me/55<?php echo $numero_limpo; ?>" target="_blank" class="btn btn-whatsapp w-100 mb-3">
                                Contatar (WhatsApp)
                            </a>
                            
                            <div class="d-flex gap-2">
                                <a href="editar_professor.php?id=<?php echo $prof['id']; ?>" class="btn btn-sm btn-outline-light w-50">Editar</a>
                                <a href="excluir_professor.php?id=<?php echo $prof['id']; ?>" class="btn btn-sm btn-outline-danger w-50" onclick="return confirm('Excluir este professor?');">Excluir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</body>
</html>
