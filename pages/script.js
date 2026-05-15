// Função para atualizar status com caminho correto
function atualizarStatus(tarefaId, novoStatus, elementoSelect) {
    console.log('Atualizando tarefa:', tarefaId, 'para:', novoStatus);
    
    // Desabilitar select
    elementoSelect.disabled = true;
    
    // Criar dados
    const formData = new FormData();
    formData.append('id', tarefaId);
    formData.append('status', novoStatus);
    
    // Usar caminho relativo para a mesma pasta
    fetch('atualizar_status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Resposta:', data);
        
        if (data.success) {
            // Mover o card
            const taskCard = elementoSelect.closest('.task-card');
            moverCardParaColuna(taskCard, novoStatus);
            
            // Atualizar contadores
            atualizarContadores();
            
            // Mostrar mensagem
            mostrarMensagem('✅ Tarefa movida com sucesso!');
            
            // Reabilitar select
            elementoSelect.disabled = false;
        } else {
            alert('Erro: ' + data.message);
            elementoSelect.disabled = false;
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro de conexão. Recarregando página...');
        location.reload();
    });
}

function moverCardParaColuna(card, status) {
    let colunaDestino;
    
    switch(status) {
        case 'a_fazer':
            colunaDestino = document.querySelector('.column-a-fazer .task-list');
            break;
        case 'fazendo':
            colunaDestino = document.querySelector('.column-fazendo .task-list');
            break;
        case 'concluido':
            colunaDestino = document.querySelector('.column-concluido .task-list');
            break;
    }
    
    if (colunaDestino) {
        colunaDestino.appendChild(card);
        card.style.animation = 'fadeIn 0.3s ease';
        setTimeout(() => {
            card.style.animation = '';
        }, 300);
    }
}

function atualizarContadores() {
    const aFazer = document.querySelector('.column-a-fazer .task-list')?.children.length || 0;
    const fazendo = document.querySelector('.column-fazendo .task-list')?.children.length || 0;
    const concluido = document.querySelector('.column-concluido .task-list')?.children.length || 0;
    
    const countAFazer = document.querySelector('.column-a-fazer .count');
    const countFazendo = document.querySelector('.column-fazendo .count');
    const countConcluido = document.querySelector('.column-concluido .count');
    
    if (countAFazer) countAFazer.textContent = aFazer;
    if (countFazendo) countFazendo.textContent = fazendo;
    if (countConcluido) countConcluido.textContent = concluido;
}

function mostrarMensagem(mensagem) {
    const msg = document.createElement('div');
    msg.textContent = mensagem;
    msg.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #48bb78;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 9999;
        animation: fadeInOut 2s ease;
    `;
    document.body.appendChild(msg);
    setTimeout(() => msg.remove(), 2000);
}

function confirmarExclusao(tarefaId) {
    if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
        window.location.href = `excluir_tarefa.php?id=${tarefaId}`;
    }
}

// Adicionar animações
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInOut {
        0% { opacity: 0; transform: translateX(100%); }
        10% { opacity: 1; transform: translateX(0); }
        90% { opacity: 1; transform: translateX(0); }
        100% { opacity: 0; transform: translateX(100%); }
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);