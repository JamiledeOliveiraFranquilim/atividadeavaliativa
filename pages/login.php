<?php
require_once '../conexao.php';

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

            header('Location: ../index.php');
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

    <link rel="stylesheet" href="../login.css">

</head>

<body>

    <div class="login-container">

        <!-- LOGO -->

        <div class="logo-central">

            <img src="../assets/image/logo.jpg" alt="Logo TaskSync" class="logo-img">

            <p>Gerencie suas tarefas com eficiência</p>

        </div>

        <!-- FORMULÁRIO -->

        <div class="form-container">

            <h2>Login</h2>

            <?php if($erro): ?>

                <div class="alert alert-error">
                    <?= $erro ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="form-group">

                    <label>E-mail</label>

                    <input 
                        type="email" 
                        name="email" 
                        required 
                        placeholder="Digite seu e-mail"
                    >

                </div>

                <div class="form-group">

                    <label>Senha</label>

                    <input 
                        type="password" 
                        name="senha" 
                        required 
                        placeholder="Digite sua senha"
                    >

                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Entrar
                </button>

            </form>

            <div class="link-cadastro">

                <p>
                    Não tem uma conta?
                    <a href="cadastro_usuario.php">Cadastre-se</a>
                </p>

            </div>

        </div>

    </div>

</body>
</html>