<?php
session_start();
require '../conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: ../index.php");
    exit;
}

$aluno_id = $_SESSION['aluno_id'];

$stmt = $pdo->prepare("SELECT * FROM alunos WHERE id = :id");
$stmt->execute(['id' => $aluno_id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

function formatarTelefone($telefone) {
    $num = preg_replace('/[^0-9]/', '', $telefone);
    if (strlen($num) == 11) {
        return '(' . substr($num, 0, 2) . ') ' . substr($num, 2, 5) . '-' . substr($num, 7);
    } elseif (strlen($num) == 10) {
        return '(' . substr($num, 0, 2) . ') ' . substr($num, 2, 4) . '-' . substr($num, 6);
    }
    return $telefone;
}

$data_nascimento_formatada = "";
$idade_texto = "Não informada";

if (!empty($aluno['data_nascimento'])) {
    $data_nasc = new DateTime($aluno['data_nascimento']);
    $hoje = new DateTime();
    $idade = $hoje->diff($data_nasc)->y;
    $data_nascimento_formatada = $data_nasc->format('d/m/Y');
    $idade_texto = $idade . " anos (" . $data_nascimento_formatada . ")";
}

$membro_desde = "Não informada";
if (!empty($aluno['data_cadastro'])) {
    $membro_desde = date('d/m/Y', strtotime($aluno['data_cadastro']));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Corpo e Forma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { 
            background-color: #121212; 
            color: #ffffff; 
            padding-bottom: 90px; 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .profile-header {
            text-align: center;
            padding: 30px 20px 20px;
        }
        
        .avatar-circle {
            width: 90px;
            height: 90px;
            background-color: #1e1e1e;
            border: 2px solid #f97316;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: #f97316;
            font-size: 2.8em;
        }

        .badge-membro {
            background-color: #1e1e1e;
            color: #b0b0b0;
            border: 1px solid #333;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 400;
            display: inline-block;
            margin-top: 5px;
        }

        .info-card {
            background-color: #1e1e1e;
            border: 1px solid #2d2d2d;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .info-icon {
            font-size: 1.5em;
            color: #f97316;
            margin-right: 15px;
            width: 30px;
            text-align: center;
        }

        .info-label {
            color: #888888;
            font-size: 0.75em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .info-value {
            color: #ffffff;
            font-weight: 600;
            font-size: 1em;
            margin: 0;
        }

        .btn-logout {
            background-color: #ef4444;
            color: white;
            font-weight: 600;
            padding: 14px;
            border-radius: 12px;
            border: none;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            margin-top: 25px;
            transition: background 0.2s;
        }

        .btn-logout:hover {
            background-color: #dc2626;
            color: white;
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

    <div class="profile-header">
        <div class="avatar-circle">
            <i class="bi bi-person-fill"></i>
        </div>
        <h3 class="fw-bold mb-1" style="color: #ffffff;"><?php echo htmlspecialchars($aluno['nome']); ?></h3>
        <span class="badge-membro">Membro desde <?php echo $membro_desde; ?></span>
    </div>

    <div class="container px-3">
        
        <div class="info-card">
            <div class="info-icon">
                <i class="bi bi-envelope"></i>
            </div>
            <div>
                <div class="info-label">E-mail</div>
                <div class="info-value"><?php echo htmlspecialchars($aluno['email']); ?></div>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <i class="bi bi-whatsapp"></i>
            </div>
            <div>
                <div class="info-label">Telefone</div>
                <div class="info-value"><?php echo formatarTelefone($aluno['telefone']); ?></div>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <i class="bi bi-calendar3"></i>
            </div>
            <div>
                <div class="info-label">Idade</div>
                <div class="info-value"><?php echo $idade_texto; ?></div>
            </div>
        </div>

        <a href="logout.php" class="btn-logout">
            <i class="bi bi-box-arrow-right"></i> Sair da Conta
        </a>

    </div>

    <div class="bottom-nav">
        <a href="painel_aluno.php" class="nav-item">
            <i class="bi bi-house-door"></i>
            Início
        </a>
        <a href="perfil.php" class="nav-item active">
            <i class="bi bi-person-fill"></i>
            Perfil
        </a>
    </div>

</body>
</html>