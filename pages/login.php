<?php
require_once 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    
    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos!';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_setor'] = $usuario['setor'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];
            
            header('Location: index.php');
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
        .logo-central {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-central h1 {
            color: #667eea;
            font-size: 36px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-central">
            <h1>📋 TaskSync</h1>
            <p>Gerencie suas tarefas com eficiência</p>
        </div>
        
        <div class="form-container">
            <h2>🔐 Login</h2>
            
            <?php if($erro): ?>
                <div class="alert alert-error"><?= $erro ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" required placeholder="Digite seu e-mail">
                </div>
                
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="senha" required placeholder="Digite sua senha">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </form>
            
            <div style="text-align: center; margin-top: 20px;">
                <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
            </div>
            
            <div style="margin-top: 20px; padding: 10px; background: #f7fafc; border-radius: 8px;">
                <p style="font-size: 12px; color: #666;">📝 Credenciais de teste:</p>
                <p style="font-size: 12px; color: #666;">Admin: admin@tasksync.com / admin123</p>
                <p style="font-size: 12px; color: #666;">Usuário: joao@tasksync.com / 123456</p>
            </div>
        </div>
    </div>
</body>
</html>