<?php
session_start();
require 'conexao.php';

$erro = '';
$sucesso = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $data_nascimento = $_POST['data_nascimento'];
    $sexo = $_POST['sexo'];
    $senha = trim($_POST['senha']);
    $data_maxima = date('Y-m-d'); 
    $data_minima = date('Y-m-d', strtotime('-100 years')); 

    $stmt = $pdo->prepare("SELECT id FROM alunos WHERE email = :email");
    $stmt->execute(['email' => $email]);
    
    if ($stmt->fetch()) {
        $erro = "Esse e-mail já está cadastrado! Tente fazer login.";
    } else {
        $sql = "INSERT INTO alunos (nome, email, telefone, data_nascimento, sexo, senha, data_cadastro) 
                VALUES (:nome, :email, :telefone, :data_nascimento, :sexo, :senha, CURDATE())";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([
            'nome' => $nome, 'email' => $email, 'telefone' => $telefone, 
            'data_nascimento' => $data_nascimento, 'sexo' => $sexo, 'senha' => $senha
        ])) {
            $_SESSION['aluno_id'] = $pdo->lastInsertId();
            $_SESSION['aluno_nome'] = $nome;
            $_SESSION['tipo_usuario'] = 'aluno';
            
            header("Location: aluno/painel_aluno.php");
            exit;
        } else {
            $erro = "Erro ao criar conta. Tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #121212; color: #ffffff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .login-box { background-color: #1e1e1e; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); width: 100%; max-width: 500px; border-top: 5px solid #f97316; }
        .form-control, .form-select { background-color: #2a2a2a; border: 1px solid #444; color: white; padding: 10px; }
        .login-box label, .login-box .text-muted, .login-box p { color: #e0e0e0 !important;}
        .form-control:focus, .form-select:focus { background-color: #333; border-color: #f97316; color: white; box-shadow: none; }
        .btn-login { background-color: #f97316; color: white; font-weight: bold; padding: 12px; width: 100%; border: none; border-radius: 8px; transition: 0.3s; }
        .btn-login:hover { background-color: #e06512; color: white; }
    </style>
</head>
<body>

    <div class="login-box">
        <div class="text-center mb-4">
            <h3 style="color: #f97316; font-weight: bold;">Criar Minha Conta</h3>
            <p class="text-muted">Preencha seus dados para acessar o app</p>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-danger" style="background-color: #ef4444; color: white; border: none;">
                <i class="bi bi-exclamation-triangle"></i> <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <form action="criar_conta.php" method="POST">
            <div class="mb-3">
                <label class="text-muted mb-1" style="font-size: 0.9em;">Nome Completo</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-1" style="font-size: 0.9em;">E-mail</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-1" style="font-size: 0.9em;">Telefone (WhatsApp)</label>
                    <input type="text" name="telefone" class="form-control" maxlength="11" placeholder="Ex: 51999999999" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-1" style="font-size: 0.9em;">Data de Nascimento</label>
                    <input type="date" name="data_nascimento" class="form-control" min="<?php echo $data_minima; ?>" max="<?php echo $data_maxima; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-1" style="font-size: 0.9em;">Sexo</label>
                    <select name="sexo" class="form-select" required>
                        <option value="">Selecione...</option>
                        <option value="M">Masculino</option>
                        <option value="F">Feminino</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="text-light">Qual o seu Objetivo?</label>
                      <select name="objetivo" class="form-select" required>
                      <option value="">Selecione...</option>
                      <option value="Hipertrofia">Hipertrofia (Ganhar Massa)</option>
                      <option value="Emagrecimento">Emagrecimento (Perder Peso)</option>
                      <option value="Saude">Saúde e Qualidade de Vida</option>
                      <option value="Condicionamento">Condicionamento Físico</option>
                      </select>
                </div>
             </div>
            <div class="mb-4">
                <label class="text-muted mb-1" style="font-size: 0.9em;">Crie uma Senha</label>
                <input type="password" name="senha" class="form-control" required>
            </div>

            <button type="submit" class="btn-login mb-3">Cadastrar e Entrar</button>
            
            <div class="text-center mt-2">
                <a href="index.php" style="color: #aaaaaa; text-decoration: none;">Já tem uma conta? <span style="color: #f97316;">Faça login</span></a>
            </div>
        </form>
    </div>

</body>
</html>