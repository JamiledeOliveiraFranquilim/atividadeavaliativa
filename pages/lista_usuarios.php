<?php
require_once 'conexao.php';
verificarAdmin();

$mensagem = '';
$erro = '';

// Buscar todos os usuários
$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY data_cadastro DESC")->fetchAll();

// Excluir usuário
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    if ($id != $_SESSION['usuario_id']) {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $mensagem = 'Usuário excluído com sucesso!';
        header('Location: lista_usuarios.php');
        exit;
    } else {
        $erro = 'Você não pode excluir seu próprio usuário!';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Lista de Usuários</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <h1>📋 TaskSync</h1>
                <p>👑 Administrador - Gerenciar Usuários</p>
            </div>
            <div class="nav-buttons">
                <a href="index.php" class="btn btn-primary">← Voltar</a>
                <a href="logout.php" class="btn btn-danger">🚪 Sair</a>
            </div>
        </div>

        <div class="form-container" style="max-width: 1000px;">
            <h2>👥 Lista de Usuários</h2>
            
            <?php if($mensagem): ?>
                <div class="alert alert-success"><?= $mensagem ?></div>
            <?php endif; ?>
            
            <?php if($erro): ?>
                <div class="alert alert-error"><?= $erro ?></div>
            <?php endif; ?>
            
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #667eea; color: white;">
                        <th style="padding: 10px;">ID</th>
                        <th style="padding: 10px;">Nome</th>
                        <th style="padding: 10px;">E-mail</th>
                        <th style="padding: 10px;">Setor</th>
                        <th style="padding: 10px;">Tipo</th>
                        <th style="padding: 10px;">Data Cadastro</th>
                        <th style="padding: 10px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $usuario): ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 10px;"><?= $usuario['id'] ?></td>
                            <td style="padding: 10px;"><?= htmlspecialchars($usuario['nome']) ?></td>
                            <td style="padding: 10px;"><?= htmlspecialchars($usuario['email']) ?></td>
                            <td style="padding: 10px;"><?= htmlspecialchars($usuario['setor']) ?></td>
                            <td style="padding: 10px;">
                                <?= $usuario['tipo'] == 'admin' ? '👑 Admin' : '👤 Usuário' ?>
                            </td>
                            <td style="padding: 10px;"><?= date('d/m/Y', strtotime($usuario['data_cadastro'])) ?></td>
                            <td style="padding: 10px;">
                                <?php if($usuario['id'] != $_SESSION['usuario_id']): ?>
                                    <a href="?excluir=<?= $usuario['id'] ?>" 
                                       onclick="return confirm('Tem certeza?')" 
                                       class="btn btn-danger btn-sm">Excluir</a>
                                <?php else: ?>
                                    <span class="badge">Você</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>