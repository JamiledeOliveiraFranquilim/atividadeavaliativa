<?php
require_once '../conexao.php';
verificarLogin();

$id = $_GET['id'] ?? 0;

if ($id) {
    // Verificar se o usuário tem permissão para excluir
    $stmt = $pdo->prepare("SELECT usuario_id FROM tarefas WHERE id = ?");
    $stmt->execute([$id]);
    $tarefa = $stmt->fetch();
    
    if ($tarefa && ($_SESSION['usuario_tipo'] == 'admin' || $tarefa['usuario_id'] == $_SESSION['usuario_id'])) {
        $stmt = $pdo->prepare("DELETE FROM tarefas WHERE id = ?");
        $stmt->execute([$id]);
    }
}

header('Location: ../index.php');
exit;