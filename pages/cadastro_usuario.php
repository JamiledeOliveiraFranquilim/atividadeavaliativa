<?php
require_once '../conexao.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $setor = trim($_POST['setor']);
    
    // Validações
    if (empty($nome) || empty($email) || empty($senha) || empty($setor)) {
        $erro = 'Todos os campos são obrigatórios!';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não coincidem!';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter no mínimo 6 caracteres!';
    } else {
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $erro = 'Este e-mail já está cadastrado!';
        } else {
            // Criptografar senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            
            // Inserir usuário
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, setor, tipo) VALUES (?, ?, ?, ?, 'user')");
            if ($stmt->execute([$nome, $email, $senha_hash, $setor])) {
                $sucesso = 'Cadastro realizado com sucesso! Faça login para continuar.';
                // Limpar formulário
                $_POST = [];
            } else {
                $erro = 'Erro ao cadastrar. Tente novamente!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Cadastro</title>
    <link rel="stylesheet" href="cadastro_usuario.css">
</head>
<body>
    <div class="container">
        <div class="form-container" style="max-width: 500px;">
            <h2>📝 Criar Conta</h2>
            
            <?php if($sucesso): ?>
                <div class="alert alert-success"><?= $sucesso ?></div>
                <script>
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                </script>
            <?php endif; ?>
            
            <?php if($erro): ?>
                <div class="alert alert-error"><?= $erro ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Nome Completo *</label>
                    <input type="text" name="nome" required value="<?= $_POST['nome'] ?? '' ?>">
                </div>
                
                <div class="form-group">
                    <label>E-mail *</label>
                    <input type="email" name="email" required value="<?= $_POST['email'] ?? '' ?>">
                </div>
                
                <div class="form-group">
                    <label>Senha * (mínimo 6 caracteres)</label>
                    <input type="password" name="senha" required>
                </div>
                
                <div class="form-group">
                    <label>Confirmar Senha *</label>
                    <input type="password" name="confirmar_senha" required>
                </div>
                
                <div class="form-group">
                    <label>Setor *</label>
                    <select name="setor" required>
                        <option value="">Selecione o setor</option>
                        <option>Desenvolvimento</option>
                        <option>Design</option>
                        <option>Marketing</option>
                        <option>Vendas</option>
                        <option>RH</option>
                        <option>Financeiro</option>
                        <option>Suporte</option>
                        <option>Administração</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
            </form>
            
            <div style="text-align: center; margin-top: 20px;">
                <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
            </div>
        </div>
    </div>
</body>
</html>