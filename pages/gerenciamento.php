<?php
require_once '../conexao.php';
$mensagem = getMensagem();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Tarefas - TaskSync</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header class="header">
        <div class="nav-container">
            <div class="logo">Task<span>Sync</span></div>
            <nav class="nav-links">
                <a href="index.php">Início</a>
                <a href="cadastro_usuario.php">Cadastrar Usuário</a>
                <a href="cadastro_tarefa.php">Cadastrar Tarefa</a>
                <a href="gerenciamento.php">Gerenciar Tarefas</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card-header">
            <h2>Quadro de Tarefas</h2>
        </div>

        <?php if($mensagem): ?>
            <div class="alert alert-<?php echo $mensagem['tipo']; ?>">
                <?php echo $mensagem['texto']; ?>
            </div>
        <?php endif; ?>

        <div class="kanban-board">
            <div class="kanban-column column-a-fazer">
                <div class="column-header">
                    <h3>A Fazer</h3>
                </div>
                <div id="aFazerTasks"></div>
            </div>

            <div class="kanban-column column-fazendo">
                <div class="column-header">
                    <h3>Fazendo</h3>
                </div>
                <div id="FazendoTasks"></div>
            </div>

            <div class="kanban-column column-concluido">
                <div class="column-header">
                    <h3>Concluído</h3>
                </div>
                <div id="ConcluidoTasks"></div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        loadTasks();
    </script>
</body>
</html>