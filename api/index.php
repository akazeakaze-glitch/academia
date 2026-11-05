<?php
session_start();
require 'conexao.php'; 

$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if ($usuario['senha'] === $senha) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE usuarios SET senha = :hash WHERE id = :id");
            $update->execute(['hash' => $senha_hash, 'id' => $usuario['id']]);
            $usuario['senha'] = $senha_hash; 
        }

        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['tipo_usuario'] = $usuario['perfil']; 
            
            header("Location: painel.php");
            exit;
        }
    }

    $stmt_aluno = $pdo->prepare("SELECT * FROM alunos WHERE email = :email");
    $stmt_aluno->execute(['email' => $email]);
    $aluno = $stmt_aluno->fetch(PDO::FETCH_ASSOC);

    if ($aluno) {
        if ($aluno['senha'] === $senha) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE alunos SET senha = :hash WHERE id = :id");
            $update->execute(['hash' => $senha_hash, 'id' => $aluno['id']]);
            $aluno['senha'] = $senha_hash;
        }

        if (password_verify($senha, $aluno['senha'])) {
            $_SESSION['aluno_id'] = $aluno['id'];
            $_SESSION['aluno_nome'] = $aluno['nome'];
            $_SESSION['tipo_usuario'] = 'aluno';
            
            header("Location: aluno/painel_aluno.php");
            exit;
        }
    }

    $erro = "E-mail ou senha incorretos!";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corpo e Forma - Academia & Fitness</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0d0d0d;
            color: #ffffff;
        }

        .hero-bg {
            background: linear-gradient(to right, rgba(0, 0, 0, 0.95) 30%, rgba(0, 0, 0, 0.5) 100%), 
                        url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1920&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px;
        }

        .brand-logo {
            font-size: 1.8em;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .brand-logo span {
            color: #f97316;
        }

        .hero-content {
            max-width: 650px;
            margin-top: auto;
            margin-bottom: auto;
        }

        .hero-subtitle {
            color: #f97316;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-size: 0.9em;
            margin-bottom: 15px;
        }

        .hero-title {
            font-size: 3.5em;
            font-weight: 900;
            line-height: 1.1;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        
        .hero-title span {
            color: #f97316;
            -webkit-text-stroke: 1px #f97316;
        }

        .hero-description {
            color: #cccccc;
            font-size: 1.1em;
            margin-bottom: 35px;
            line-height: 1.6;
        }

        .btn-custom-primary {
            background-color: #f97316;
            color: #ffffff;
            font-weight: 800;
            padding: 14px 32px;
            border-radius: 8px;
            text-decoration: none;
            border: 2px solid #f97316;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }

        .btn-custom-primary:hover {
            background-color: #e06512;
            border-color: #e06512;
            color: #ffffff;
            transform: translateY(-2px);
        }

        .btn-custom-outline {
            background-color: transparent;
            color: #ffffff;
            font-weight: 800;
            padding: 14px 32px;
            border-radius: 8px;
            text-decoration: none;
            border: 2px solid #ffffff;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-left: 15px;
            transition: all 0.3s;
        }

        .btn-custom-outline:hover {
            background-color: #ffffff;
            color: #000000;
            transform: translateY(-2px);
        }

        .modal-content {
            background-color: #1e1e1e;
            border: 1px solid #333;
            border-top: 5px solid #f97316;
            border-radius: 15px;
            color: #ffffff;
        }

        .form-control {
            background-color: #2a2a2a;
            border: 1px solid #444;
            color: #ffffff;
            padding: 12px;
        }

        .form-control:focus {
            background-color: #333;
            border-color: #f97316;
            color: #ffffff;
            box-shadow: none;
        }

        .input-group-text {
            background-color: #2a2a2a;
            border: 1px solid #444;
            color: #888888;
        }

        @media (max-width: 768px) {
            .hero-bg { padding: 20px; }
            .hero-title { font-size: 2.4em; }
            .btn-custom-outline { margin-left: 0; margin-top: 15px; width: 100%; text-align: center; }
            .btn-custom-primary { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>

    <div class="hero-bg">
        <div class="d-flex justify-content-between align-items-center">
            <div class="brand-logo">
                CORPO <span>E FORMA</span>
            </div>
            <div>
                <button class="btn btn-outline-light btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <i class="bi bi-person-fill"></i> ÁREA DO ALUNO
                </button>
            </div>
        </div>

        <div class="hero-content">
            <div class="hero-subtitle">Centro de Treinamento & Performance</div>
            <h1 class="hero-title">
                TRANSFORME SEU CORPO. <br>
                <span>SUPERE LIMITES.</span>
            </h1>
            <p class="hero-description">
                Acesse suas fichas de treino personalizadas, acompanhe sua evolução física em tempo real e atinja seus objetivos com a melhor estrutura.
            </p>

            <div>
                <a href="#" class="btn-custom-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
                </a>

                <a href="criar_conta.php" class="btn-custom-outline">
                    Criar Minha Conta
                </a>
            </div>
        </div>

        <div class="text-muted small">
            &copy; <?php echo date('Y'); ?> Corpo e Forma. Todos os direitos reservados.
        </div>
    </div>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold" id="loginModalLabel" style="color: #f97316;">Acesso ao Sistema</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <?php if ($erro): ?>
                        <div class="alert alert-danger text-center" role="alert" style="background-color: #ef4444; color: white; border: none;">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo $erro; ?>
                        </div>
                    <?php endif; ?>

                    <form action="index.php" method="POST">
                        <div class="mb-3">
                            <label class="text-muted mb-1" style="font-size: 0.9em;">E-mail de Acesso</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted mb-1" style="font-size: 0.9em;">Sua Senha</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="senha" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" class="btn-custom-primary w-100 border-0 mb-3">
                            Entrar na Minha Conta
                        </button>
                        
                        <div class="text-center">
                            <span class="text-muted" style="font-size: 0.85em;">Novo por aqui?</span><br>
                            <a href="criar_conta.php" style="color: #f97316; text-decoration: none; font-weight: bold; font-size: 0.9em;">
                                Criar minha conta agora
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <?php if ($erro): ?>
    <script>
        var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
    </script>
    <?php endif; ?>

</body>
</html>