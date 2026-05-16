<?php
require_once '../conexao.php';
verificarLogin();

$id = $_GET['id'] ?? 0;
$erro = '';
$mensagem = '';

// Buscar tarefa e verificar permissão (COM JOIN para pegar o nome do usuário)
$stmt = $pdo->prepare("
    SELECT t.*, u.nome as usuario_nome 
    FROM tarefas t 
    JOIN usuarios u ON t.usuario_id = u.id 
    WHERE t.id = ?
");
$stmt->execute([$id]);
$tarefa = $stmt->fetch();

if (!$tarefa) {
    header('Location: ../index.php');
    exit;
}

// Verificar se o usuário tem permissão para editar (admin ou dono da tarefa)
if ($_SESSION['usuario_tipo'] != 'admin' && $tarefa['usuario_id'] != $_SESSION['usuario_id']) {
    header('Location: ../index.php');
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
            // Recarregar dados COM O JOIN novamente
            $stmt = $pdo->prepare("
                SELECT t.*, u.nome as usuario_nome 
                FROM tarefas t 
                JOIN usuarios u ON t.usuario_id = u.id 
                WHERE t.id = ?
            ");
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
    <link rel="stylesheet" href="cadastro_tarefa.css">
</head>
<body>
    <!-- Navbar no Topo Centralizada (MESMA DO CADASTRO) -->
    <div class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <img src="../assets/image/logo.jpg" alt="Logo TaskSync" class="logo-img">
                <div class="logo-text">
                    <h1>TaskSync</h1>
                    <p>SEU GERENCIADOR DE TAREFAS</p>
                </div>
            </div>
            <div class="nav-links">
                <a href="../index.php" class="nav-btn nav-btn-primary">Voltar ao Kanban</a>
                <a href="logout.php" class="nav-btn nav-btn-danger">Sair</a>
            </div>
        </div>
    </div>

    <div class="form-container" style="max-width: 700px;">
        <h2>Editar Tarefa</h2>
        <p style="text-align: center; color: #8898b0; margin-bottom: 20px;">
            Editando tarefa de <?= htmlspecialchars($tarefa['usuario_nome'] ?? $tarefa['usuario_id']) ?>
        </p>
        
        <?php if($mensagem): ?>
            <div class="alert alert-success"><?= $mensagem ?></div>
        <?php endif; ?>
        
        <?php if($erro): ?>
            <div class="alert alert-error"><?= $erro ?></div>
        <?php endif; ?>
        
        <form method="POST" id="formTarefa">
            <div class="form-group">
                <label>Descrição da Tarefa *</label>
                <textarea name="descricao" id="descricao" required 
                          placeholder="Descreva a tarefa em detalhes..." 
                          rows="3"><?= htmlspecialchars($tarefa['descricao']) ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Setor *</label>
                    <select name="setor" id="setor" required>
                        <option value="<?= $tarefa['setor'] ?>"><?= $tarefa['setor'] ?> (Atual)</option>
                        <option value="Desenvolvimento">Desenvolvimento</option>
                        <option value="Design">Design</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Vendas">Vendas</option>
                        <option value="RH">RH</option>
                        <option value="Financeiro">Financeiro</option>
                        <option value="Suporte">Suporte</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Prioridade *</label>
                    <select name="prioridade" id="prioridade" required>
                        <option value="baixa" <?= $tarefa['prioridade'] == 'baixa' ? 'selected' : '' ?>>Baixa</option>
                        <option value="media" <?= $tarefa['prioridade'] == 'media' ? 'selected' : '' ?>>Média</option>
                        <option value="alta" <?= $tarefa['prioridade'] == 'alta' ? 'selected' : '' ?>>Alta</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Status *</label>
                <select name="status" required>
                    <option value="a_fazer" <?= $tarefa['status'] == 'a_fazer' ? 'selected' : '' ?>>A Fazer</option>
                    <option value="fazendo" <?= $tarefa['status'] == 'fazendo' ? 'selected' : '' ?>>Fazendo</option>
                    <option value="concluido" <?= $tarefa['status'] == 'concluido' ? 'selected' : '' ?>>Concluído</option>
                </select>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-primary">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>

    <script>
        function mostrarToast(mensagem) {
            const toast = document.createElement('div');
            toast.className = 'toast-message';
            toast.textContent = mensagem;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        }
        
        let formModified = false;
        document.getElementById('descricao').addEventListener('input', () => {
            formModified = true;
        });
        
        document.getElementById('setor').addEventListener('change', () => {
            formModified = true;
        });
        
        document.getElementById('prioridade').addEventListener('change', () => {
            formModified = true;
        });
        
        window.addEventListener('beforeunload', (e) => {
            if (formModified) {
                e.preventDefault();
                e.returnValue = 'Você tem alterações não salvas. Deseja realmente sair?';
            }
        });
        
        document.getElementById('formTarefa').addEventListener('submit', () => {
            formModified = false;
        });
        
        <?php if($mensagem): ?>
        mostrarToast('<?= $mensagem ?>');
        <?php endif; ?>
    </script>
</body>
</html>