<?php
require_once __DIR__ . '/../includes/conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Sistema de Gerenciamento de Tarefas</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
        <div class="card">
            <div class="card-header">
                <h2>Bem-vindo ao TaskSync</h2>
            </div>
            <div style="text-align: center; padding: 2rem;">
                <h3>Gerencie suas tarefas de forma eficiente</h3>
                <p style="margin-top: 1rem; color: #6b7280;">
                    Organize suas atividades, acompanhe o progresso e aumente a produtividade da sua equipe.
                </p>
                <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="cadastro_usuario.php" class="btn btn-primary">Cadastrar Usuário</a>
                    <a href="cadastro_tarefa.php" class="btn btn-success">Cadastrar Tarefa</a>
                    <a href="gerenciamento.php" class="btn btn-info">Ver Tarefas</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>