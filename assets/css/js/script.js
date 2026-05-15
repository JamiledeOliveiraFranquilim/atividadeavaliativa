// Funções utilitárias
function showMessage(message, type = 'success') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert alert-${type}`;
    messageDiv.textContent = message;
    
    const container = document.querySelector('.container');
    container.insertBefore(messageDiv, container.firstChild);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}

// Confirmar exclusão
function confirmDelete(taskId) {
    if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
        window.location.href = `actions/excluir_tarefa.php?id=${taskId}`;
    }
}

// Carregar tarefas dinamicamente (para a página de gerenciamento)
function loadTasks() {
    fetch('api/get_tarefas.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateKanbanBoard(data.tarefas);
            }
        })
        .catch(error => console.error('Erro:', error));
}

function updateKanbanBoard(tarefas) {
    const columns = {
        'a_fazer': document.getElementById('aFazerTasks'),
        'fazendo': document.getElementById('FazendoTasks'),
        'concluido': document.getElementById('ConcluidoTasks')
    };
    
    // Limpar colunas
    Object.values(columns).forEach(col => {
        if (col) col.innerHTML = '';
    });
    
    // Preencher tarefas
    tarefas.forEach(tarefa => {
        const taskCard = createTaskCard(tarefa);
        const columnKey = tarefa.status === 'a_fazer' ? 'a_fazer' : 
                         (tarefa.status === 'fazendo' ? 'fazendo' : 'concluido');
        if (columns[columnKey]) {
            columns[columnKey].appendChild(taskCard);
        }
    });
}

function createTaskCard(tarefa) {
    const div = document.createElement('div');
    div.className = 'task-card';
    
    const prioridadeClass = {
        'alta': 'priority-high',
        'media': 'priority-medium',
        'baixa': 'priority-low'
    }[tarefa.prioridade];
    
    div.innerHTML = `
        <div class="task-title">${escapeHtml(tarefa.usuario_nome)}</div>
        <div class="task-description">${escapeHtml(tarefa.descricao)}</div>
        <div class="task-meta">
            <span class="badge ${prioridadeClass}">${tarefa.prioridade.toUpperCase()}</span>
            <span class="badge setor-badge">${escapeHtml(tarefa.setor)}</span>
        </div>
        <div class="task-actions">
            <a href="editar_tarefa.php?id=${tarefa.id}" class="btn btn-info btn-sm">Editar</a>
            <button onclick="confirmDelete(${tarefa.id})" class="btn btn-danger btn-sm">Excluir</button>
    `;
    
    // Botão alterar status (exceto para concluídas)
    if (tarefa.status !== 'concluido') {
        const nextStatus = tarefa.status === 'a_fazer' ? 'fazendo' : 'concluido';
        const nextStatusText = tarefa.status === 'a_fazer' ? 'Iniciar' : 'Concluir';
        div.innerHTML += `<a href="actions/alterar_status.php?id=${tarefa.id}&status=${nextStatus}" class="btn btn-success btn-sm">${nextStatusText}</a>`;
    }
    
    div.innerHTML += `</div>`;
    
    return div;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Atualizar tarefas automaticamente a cada 30 segundos
if (window.location.pathname.includes('gerenciamento.php')) {
    setInterval(loadTasks, 30000);
}