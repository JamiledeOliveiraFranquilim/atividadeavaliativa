<?php
require_once '../conexao.php';
verificarLogin();

$mensagem = '';
$erro = '';

$usuario_id = $_SESSION['usuario_id'];
$usuario_setor = $_SESSION['usuario_setor'];
$usuario_nome = $_SESSION['usuario_nome'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = trim($_POST['descricao']);
    $setor = trim($_POST['setor']);
    $prioridade = $_POST['prioridade'];
    $acao = $_POST['acao'] ?? 'cadastrar';
    
    if (empty($descricao) || empty($setor) || empty($prioridade)) {
        $erro = 'Todos os campos são obrigatórios!';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO tarefas (usuario_id, descricao, setor, prioridade, status) VALUES (?, ?, ?, ?, 'a_fazer')");
            $stmt->execute([$usuario_id, $descricao, $setor, $prioridade]);
            
            if ($acao == 'cadastrar_mais') {
                $mensagem = 'Tarefa cadastrada com sucesso! Continue cadastrando outra tarefa.';
                $_POST['descricao'] = '';
            } else {
                $mensagem = 'Tarefa cadastrada com sucesso!';
                echo "<script>setTimeout(() => { window.location.href = '../index.php'; }, 1500);</script>";
            }
        } catch(PDOException $e) {
            $erro = 'Erro ao cadastrar: ' . $e->getMessage();
        }
    }
}

// Buscar últimas tarefas do usuário
$stmt = $pdo->prepare("
    SELECT * FROM tarefas 
    WHERE usuario_id = ? 
    ORDER BY data_cadastro DESC 
    LIMIT 5
");
$stmt->execute([$usuario_id]);
$ultimas_tarefas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Nova Tarefa</title>
    <link rel="stylesheet" href="cadastro_tarefa.css">
</head>
<body>
  <!-- Navbar no Topo Centralizada -->
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
        </div>

        <div class="form-container" style="max-width: 700px;">
            <h2>Nova Tarefa</h2>
            <p style="text-align: center; color: #7F8C8D; margin-bottom: 20px;">
                Olá, <?= htmlspecialchars($usuario_nome) ?>! Adicione suas tarefas abaixo.
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
                              rows="3"><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Setor *</label>
                        <select name="setor" id="setor" required>
                            <option value="<?= $usuario_setor ?>"><?= $usuario_setor ?> (Meu Setor)</option>
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
                            <option value="baixa">Baixa</option>
                            <option value="media" selected>Média</option>
                            <option value="alta">Alta</option>
                        </select>
                    </div>
                </div>
                
                <div class="button-group">
                    <button type="submit" name="acao" value="cadastrar" class="btn btn-primary">
                        Cadastrar e Ver Kanban
                    </button>
                    <button type="submit" name="acao" value="cadastrar_mais" class="btn btn-secondary">
                        Cadastrar e Continuar
                    </button>
                </div>
            </form>
            
            <?php if(count($ultimas_tarefas) > 0): ?>
            <div class="ultimas-tarefas">
                <h3>Ultimas tarefas cadastradas</h3>
                <div class="lista-rapida">
                    <?php foreach($ultimas_tarefas as $tarefa): ?>
                        <div class="tarefa-item" onclick="reutilizarTarefa('<?= htmlspecialchars(addslashes($tarefa['descricao'])) ?>', '<?= $tarefa['setor'] ?>', '<?= $tarefa['prioridade'] ?>')">
                            <span class="tarefa-desc-preview"><?= htmlspecialchars(substr($tarefa['descricao'], 0, 30)) ?>...</span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p style="font-size: 12px; color: #7F8C8D; margin-top: 10px;">
                    Clique em uma tarefa acima para reutilizar sua descrição
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function reutilizarTarefa(descricao, setor, prioridade) {
            document.getElementById('descricao').value = descricao;
            document.getElementById('setor').value = setor;
            document.getElementById('prioridade').value = prioridade;
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            const descricaoField = document.getElementById('descricao');
            descricaoField.style.backgroundColor = '#FFF3CD';
            descricaoField.style.transition = 'background-color 0.5s';
            setTimeout(() => {
                descricaoField.style.backgroundColor = '';
            }, 1000);
            
            mostrarToast('Descrição copiada! Edite se necessário.');
        }
        
        function mostrarToast(mensagem) {
            const toast = document.createElement('div');
            toast.className = 'toast-message';
            toast.textContent = mensagem;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        }
        
        <?php if(isset($_POST['acao']) && $_POST['acao'] == 'cadastrar_mais' && !$erro): ?>
        document.getElementById('descricao').value = '';
        document.getElementById('descricao').focus();
        <?php endif; ?>
        
        let formModified = false;
        document.getElementById('descricao').addEventListener('input', () => {
            formModified = true;
        });
        
        window.addEventListener('beforeunload', (e) => {
            if (formModified && document.getElementById('descricao').value.trim() !== '') {
                e.preventDefault();
                e.returnValue = 'Você tem uma tarefa não salva. Deseja realmente sair?';
            }
        });
        
        document.getElementById('formTarefa').addEventListener('submit', () => {
            formModified = false;
        });
    </script>
</body>
</html>