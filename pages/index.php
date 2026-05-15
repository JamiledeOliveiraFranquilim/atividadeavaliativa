<?php
require_once 'conexao.php';
verificarLogin();

$usuario_id = $_SESSION['usuario_id'];
$usuario_tipo = $_SESSION['usuario_tipo'];
$usuario_nome = $_SESSION['usuario_nome'];

// Buscar tarefas
if ($usuario_tipo == 'admin') {
    $stmt = $pdo->query("
        SELECT t.*, u.nome as usuario_nome 
        FROM tarefas t 
        JOIN usuarios u ON t.usuario_id = u.id 
        ORDER BY 
            CASE t.prioridade 
                WHEN 'alta' THEN 1 
                WHEN 'media' THEN 2 
                WHEN 'baixa' THEN 3 
            END,
            t.data_cadastro DESC
    ");
} else {
    $stmt = $pdo->prepare("
        SELECT t.*, u.nome as usuario_nome 
        FROM tarefas t 
        JOIN usuarios u ON t.usuario_id = u.id 
        WHERE t.usuario_id = ?
        ORDER BY 
            CASE t.prioridade 
                WHEN 'alta' THEN 1 
                WHEN 'media' THEN 2 
                WHEN 'baixa' THEN 3 
            END,
            t.data_cadastro DESC
    ");
    $stmt->execute([$usuario_id]);
}

$tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Separar tarefas por status
$tarefas_a_fazer = array_filter($tarefas, function($t) { return $t['status'] == 'a_fazer'; });
$tarefas_fazendo = array_filter($tarefas, function($t) { return $t['status'] == 'fazendo'; });
$tarefas_concluidas = array_filter($tarefas, function($t) { return $t['status'] == 'concluido'; });
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Kanban Board</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .task-card {
            transition: all 0.3s ease;
        }
        .task-card.movendo {
            opacity: 0.5;
            transform: scale(0.95);
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .toast-message {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #48bb78;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <h1>📋 TaskSync</h1>
                <p>Olá, <?= htmlspecialchars($usuario_nome) ?>! 
                <?= $usuario_tipo == 'admin' ? '👑 Administrador' : '👤 Usuário' ?></p>
            </div>
            <div class="nav-buttons">
                <?php if($usuario_tipo == 'admin'): ?>
                    <a href="lista_usuarios.php" class="btn btn-primary">👥 Usuários</a>
                <?php endif; ?>
                <a href="cadastro_tarefa.php" class="btn btn-secondary">📝 Nova Tarefa</a>
                <a href="logout.php" class="btn btn-danger">🚪 Sair</a>
            </div>
        </div>

        <div class="kanban-board">
            <!-- Coluna A Fazer -->
            <div class="column column-a-fazer">
                <div class="column-header">
                    <h2>📌 A Fazer</h2>
                    <span class="count"><?= count($tarefas_a_fazer) ?></span>
                </div>
                <div class="task-list" id="coluna-a-fazer">
                    <?php foreach($tarefas_a_fazer as $tarefa): ?>
                        <div class="task-card" data-id="<?= $tarefa['id'] ?>" data-status="a_fazer">
                            <div class="task-desc"><?= htmlspecialchars($tarefa['descricao']) ?></div>
                            <div class="task-meta">
                                <span class="badge badge-usuario">👤 <?= htmlspecialchars($tarefa['usuario_nome']) ?></span>
                                <span class="badge badge-setor">🏢 <?= htmlspecialchars($tarefa['setor']) ?></span>
                                <span class="badge prioridade-<?= $tarefa['prioridade'] ?>">
                                    🚩 <?= ucfirst($tarefa['prioridade']) ?>
                                </span>
                                <span class="badge badge-data">📅 <?= date('d/m/Y', strtotime($tarefa['data_cadastro'])) ?></span>
                            </div>
                            <div class="task-actions">
                                <a href="editar_tarefa.php?id=<?= $tarefa['id'] ?>" class="btn btn-primary btn-sm">✏️ Editar</a>
                                <button onclick="confirmarExclusao(<?= $tarefa['id'] ?>)" class="btn btn-danger btn-sm">🗑️ Excluir</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if(count($tarefas_a_fazer) == 0): ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Coluna Fazendo -->
            <div class="column column-fazendo">
                <div class="column-header">
                    <h2>⚙️ Fazendo</h2>
                    <span class="count"><?= count($tarefas_fazendo) ?></span>
                </div>
                <div class="task-list" id="coluna-fazendo">
                    <?php foreach($tarefas_fazendo as $tarefa): ?>
                        <div class="task-card" data-id="<?= $tarefa['id'] ?>" data-status="fazendo">
                            <div class="task-desc"><?= htmlspecialchars($tarefa['descricao']) ?></div>
                            <div class="task-meta">
                                <span class="badge badge-usuario">👤 <?= htmlspecialchars($tarefa['usuario_nome']) ?></span>
                                <span class="badge badge-setor">🏢 <?= htmlspecialchars($tarefa['setor']) ?></span>
                                <span class="badge prioridade-<?= $tarefa['prioridade'] ?>">
                                    🚩 <?= ucfirst($tarefa['prioridade']) ?>
                                </span>
                                <span class="badge badge-data">📅 <?= date('d/m/Y', strtotime($tarefa['data_cadastro'])) ?></span>
                            </div>
                            <div class="task-actions">
                                <a href="editar_tarefa.php?id=<?= $tarefa['id'] ?>" class="btn btn-primary btn-sm">✏️ Editar</a>
                                <button onclick="confirmarExclusao(<?= $tarefa['id'] ?>)" class="btn btn-danger btn-sm">🗑️ Excluir</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if(count($tarefas_fazendo) == 0): ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Coluna Concluído -->
            <div class="column column-concluido">
                <div class="column-header">
                    <h2>✅ Concluído</h2>
                    <span class="count"><?= count($tarefas_concluidas) ?></span>
                </div>
                <div class="task-list" id="coluna-concluido">
                    <?php foreach($tarefas_concluidas as $tarefa): ?>
                        <div class="task-card" data-id="<?= $tarefa['id'] ?>" data-status="concluido">
                            <div class="task-desc"><?= htmlspecialchars($tarefa['descricao']) ?></div>
                            <div class="task-meta">
                                <span class="badge badge-usuario">👤 <?= htmlspecialchars($tarefa['usuario_nome']) ?></span>
                                <span class="badge badge-setor">🏢 <?= htmlspecialchars($tarefa['setor']) ?></span>
                                <span class="badge prioridade-<?= $tarefa['prioridade'] ?>">
                                    🚩 <?= ucfirst($tarefa['prioridade']) ?>
                                </span>
                                <span class="badge badge-data">📅 <?= date('d/m/Y', strtotime($tarefa['data_cadastro'])) ?></span>
                            </div>
                            <div class="task-actions">
                                <a href="editar_tarefa.php?id=<?= $tarefa['id'] ?>" class="btn btn-primary btn-sm">✏️ Editar</a>
                                <button onclick="confirmarExclusao(<?= $tarefa['id'] ?>)" class="btn btn-danger btn-sm">🗑️ Excluir</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if(count($tarefas_concluidas) == 0): ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Função principal para alterar status
    function alterarStatus(tarefaId, novoStatus, selectElement) {
        // Encontrar o card
        const card = selectElement.closest('.task-card');
        
        // Desabilitar select durante a requisição
        selectElement.disabled = true;
        
        // Adicionar classe de movimento
        card.classList.add('movendo');
        
        // Criar dados para enviar
        const formData = new FormData();
        formData.append('id', tarefaId);
        formData.append('status', novoStatus);
        
        // Enviar requisição
        fetch('atualizar_status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mover o card para a nova coluna
                moverCard(card, novoStatus);
                
                // Atualizar o data-status do card
                card.setAttribute('data-status', novoStatus);
                
                // Atualizar os contadores
                atualizarContadores();
                
                // Mostrar mensagem de sucesso
                mostrarToast('✅ Tarefa movida para ' + getStatusNome(novoStatus));
            } else {
                alert('Erro: ' + data.message);
                // Reverter o select para o status anterior
                const statusAtual = card.getAttribute('data-status');
                selectElement.value = statusAtual;
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro de conexão. Tente novamente.');
            // Reverter o select
            const statusAtual = card.getAttribute('data-status');
            selectElement.value = statusAtual;
        })
        .finally(() => {
            // Reabilitar select e remover classe
            selectElement.disabled = false;
            card.classList.remove('movendo');
        });
    }
    
    // Função para mover o card para a coluna correta
    function moverCard(card, novoStatus) {
        let colunaDestino;
        
        switch(novoStatus) {
            case 'a_fazer':
                colunaDestino = document.querySelector('#coluna-a-fazer');
                break;
            case 'fazendo':
                colunaDestino = document.querySelector('#coluna-fazendo');
                break;
            case 'concluido':
                colunaDestino = document.querySelector('#coluna-concluido');
                break;
            default:
                return;
        }
        
        if (colunaDestino) {
            // Remover mensagem "nenhuma tarefa" se existir
            const emptyMessage = colunaDestino.querySelector('div[style*="text-align: center"]');
            if (emptyMessage && colunaDestino.children.length === 1) {
                emptyMessage.remove();
            }
            
            // Adicionar o card à nova coluna
            colunaDestino.appendChild(card);
            
            // Animação de entrada
            card.style.animation = 'fadeIn 0.3s ease';
            setTimeout(() => {
                card.style.animation = '';
            }, 300);
        }
    }
    
    // Função para atualizar os contadores
    function atualizarContadores() {
        const colunas = {
            'a_fazer': document.querySelector('#coluna-a-fazer'),
            'fazendo': document.querySelector('#coluna-fazendo'),
            'concluido': document.querySelector('#coluna-concluido')
        };
        
        for (let [status, coluna] of Object.entries(colunas)) {
            if (coluna) {
                // Contar apenas elementos com classe task-card
                const count = coluna.querySelectorAll('.task-card').length;
                const header = document.querySelector(`.column-${status} .count`);
                if (header) {
                    header.textContent = count;
                }
                
                // Adicionar mensagem se estiver vazio
                if (count === 0 && !coluna.querySelector('.empty-message')) {
                    const emptyMsg = document.createElement('div');
                    emptyMsg.className = 'empty-message';
                    emptyMsg.style.cssText = 'text-align: center; color: #999; padding: 40px;';
                    emptyMsg.textContent = '📭 Nenhuma tarefa';
                    coluna.appendChild(emptyMsg);
                } else if (count > 0) {
                    const emptyMsg = coluna.querySelector('.empty-message');
                    if (emptyMsg) emptyMsg.remove();
                }
            }
        }
    }
    
    // Função para mostrar toast de notificação
    function mostrarToast(mensagem) {
        const toast = document.createElement('div');
        toast.className = 'toast-message';
        toast.textContent = mensagem;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 2000);
    }
    
    // Função para obter nome do status em português
    function getStatusNome(status) {
        const nomes = {
            'a_fazer': 'A Fazer',
            'fazendo': 'Fazendo',
            'concluido': 'Concluído'
        };
        return nomes[status] || status;
    }
    
    // Função para confirmar exclusão
    function confirmarExclusao(tarefaId) {
        if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
            window.location.href = `excluir_tarefa.php?id=${tarefaId}`;
        }
    }
    
    // Inicializar ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        atualizarContadores();
    });
    </script>
</body>
</html>