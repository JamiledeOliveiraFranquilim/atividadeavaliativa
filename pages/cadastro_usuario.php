<?php
require_once 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário - TaskSync</title>
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
                <h2>Cadastro de Usuário</h2>
            </div>

            <?php if(isset($_SESSION['mensagem'])): ?>
                <div class="alert alert-<?php echo $_SESSION['tipo']; ?>">
                    <?php 
                    echo $_SESSION['mensagem'];
                    unset($_SESSION['mensagem']);
                    unset($_SESSION['tipo']);
                    ?>
                </div>
            <?php endif; ?>

            <form action="actions/criar_usuario.php" method="POST">
                <div class="form-group">
                    <label for="nome">Nome Completo *</label>
                    <input type="text" id="nome" name="nome" required>
                </div>

                <div class="form-group">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="cargo">Cargo *</label>
                    <input type="text" id="cargo" name="cargo" required>
                </div>

                <div class="form-group">
                    <label for="setor">Setor *</label>
                    <select id="setor" name="setor" required>
                        <option value="">Selecione um setor</option>
                        <option value="Tecnologia">Tecnologia</option>
                        <option value="Design">Design</option>
                        <option value="Gestão">Gestão</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Vendas">Vendas</option>
                        <option value="RH">RH</option>
                        <option value="Financeiro">Financeiro</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Cadastrar Usuário</button>
                <a href="index.php" class="btn" style="background: #6b7280; color: white;">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>