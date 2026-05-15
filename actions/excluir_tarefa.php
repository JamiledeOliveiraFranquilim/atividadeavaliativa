<?php
require_once '../includes/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("DELETE FROM tarefas WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Tarefa excluída com sucesso!";
        $_SESSION['tipo'] = "success";
    } else {
        $_SESSION['mensagem'] = "Erro ao excluir tarefa: " . $conn->error;
        $_SESSION['tipo'] = "error";
    }
    
    $stmt->close();
    $conn->close();
    
    header("Location: ../gerenciamento.php");
    exit();
}
?>