<?php
require_once 'conexao.php';
verificarLogin();

$id = $_GET['id'] ?? 0;
$erro = '';
$mensagem = '';

// Buscar tarefa e verificar permissão
$stmt = $pdo->prepare("
    SELECT t.*, u.nome as usuario_nome 
    FROM tarefas t 
    JOIN usuarios u ON t.usuario_id = u.id 
    WHERE t.id = ?
");
$stmt->execute([$id]);
$tarefa = $stmt->fetch();

if (!$tarefa) {
    header('Location: index.php');
    exit;
}

// Verificar se o usuário tem permissão para editar (admin ou dono da tarefa)
if ($_SESSION['usuario_tipo'] != 'admin' && $tarefa['usuario_id'] != $_SESSION['usuario_id']) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = trim($_POST['descricao']);
    $setor = trim($_POST['setor']);
    $prioridade = $_POST['prioridade'];
    $status = $_POST['status'];
    
    if (empty($descricao) || empty($setor) || empty($prioridade)) {
        $erro = 'Todos os campos são obrigatórios!';
    } else {
        $stmt = $pdo->prepare("UPDATE tarefas SET descricao = ?, setor = ?, prioridade = ?, status = ? WHERE id = ?");
        if ($stmt->execute([$descricao, $setor, $prioridade, $status, $id])) {
            $mensagem = 'Tarefa atualizada com sucesso!';
            // Recarregar dados
            $stmt = $pdo->prepare("SELECT * FROM tarefas WHERE id = ?");
            $stmt->execute([$id]);
            $tarefa = $stmt->fetch();
        } else {
            $erro = 'Erro ao atualizar';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Editar Tarefa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <h1>📋 TaskSync</h1>
                <p>Editar Tarefa #<?= $id ?></p>
            </div>
            <div class="nav-buttons">
                <a href="index.php" class="btn btn-primary">← Voltar ao Kanban</a>
                <a href="logout.php" class="btn btn-danger">🚪 Sair</a>
            </div>
        </div>

        <div class="form-container">
            <h2>✏️ Editar Tarefa</h2>
            
            <?php if($mensagem): ?>
                <div class="alert alert-success"><?= $mensagem ?></div>
            <?php endif; ?>
            
            <?php if($erro): ?>
                <div class="alert alert-error"><?= $erro ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Descrição da Tarefa *</label>
                    <textarea name="descricao" required><?= htmlspecialchars($tarefa['descricao']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Setor *</label>
                    <select name="setor" required>
                        <option value="<?= $tarefa['setor'] ?>"><?= $tarefa['setor'] ?></option>
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
                        <option value="baixa" <?= $tarefa['prioridade'] == 'baixa' ? 'selected' : '' ?>>🔵 Baixa</option>
                        <option value="media" <?= $tarefa['prioridade'] == 'media' ? 'selected' : '' ?>>🟡 Média</option>
                        <option value="alta" <?= $tarefa['prioridade'] == 'alta' ? 'selected' : '' ?>>🔴 Alta</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" required>
                        <option value="a_fazer" <?= $tarefa['status'] == 'a_fazer' ? 'selected' : '' ?>>📌 A Fazer</option>
                        <option value="fazendo" <?= $tarefa['status'] == 'fazendo' ? 'selected' : '' ?>>⚙️ Fazendo</option>
                        <option value="concluido" <?= $tarefa['status'] == 'concluido' ? 'selected' : '' ?>>✅ Concluído</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">💾 Salvar Alterações</button>
            </form>
        </div>
    </div>
</body>
</html>