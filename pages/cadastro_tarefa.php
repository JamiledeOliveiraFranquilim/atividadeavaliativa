<?php
require_once 'conexao.php';

$mensagem = '';
$erro = '';

// Buscar usuários para o select
$usuarios = $pdo->query("SELECT id, nome, setor FROM usuarios ORDER BY nome")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_POST['usuario_id'];
    $descricao = trim($_POST['descricao']);
    $setor = trim($_POST['setor']);
    $prioridade = $_POST['prioridade'];
    
    if (empty($usuario_id) || empty($descricao) || empty($setor) || empty($prioridade)) {
        $erro = 'Todos os campos são obrigatórios!';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO tarefas (usuario_id, descricao, setor, prioridade, status) VALUES (?, ?, ?, ?, 'a_fazer')");
            $stmt->execute([$usuario_id, $descricao, $setor, $prioridade]);
            $mensagem = 'Tarefa cadastrada com sucesso!';
        } catch(PDOException $e) {
            $erro = 'Erro ao cadastrar: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Cadastro de Tarefa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <h1>📋 TaskSync</h1>
                <p>Cadastro de Tarefa</p>
            </div>
            <div class="nav-buttons">
                <a href="index.php" class="btn btn-primary">← Voltar ao Kanban</a>
            </div>
        </div>

        <div class="form-container">
            <h2>📝 Nova Tarefa</h2>
            
            <?php if($mensagem): ?>
                <div class="alert alert-success"><?= $mensagem ?></div>
            <?php endif; ?>
            
            <?php if($erro): ?>
                <div class="alert alert-error"><?= $erro ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Usuário Responsável *</label>
                    <select name="usuario_id" required>
                        <option value="">Selecione um usuário</option>
                        <?php foreach($usuarios as $usuario): ?>
                            <option value="<?= $usuario['id'] ?>">
                                <?= htmlspecialchars($usuario['nome']) ?> (<?= htmlspecialchars($usuario['setor']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Descrição da Tarefa *</label>
                    <textarea name="descricao" required placeholder="Descreva a tarefa em detalhes..."></textarea>
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
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Prioridade *</label>
                    <select name="prioridade" required>
                        <option value="">Selecione a prioridade</option>
                        <option value="baixa">🔵 Baixa</option>
                        <option value="media">🟡 Média</option>
                        <option value="alta">🔴 Alta</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-secondary btn-block">✅ Cadastrar Tarefa</button>
            </form>
        </div>
    </div>
</body>
</html>