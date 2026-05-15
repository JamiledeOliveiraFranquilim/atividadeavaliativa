<?php
require_once '../includes/config.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("UPDATE tarefas SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Status da tarefa atualizado com sucesso!";
        $_SESSION['tipo'] = "success";
    } else {
        $_SESSION['mensagem'] = "Erro ao atualizar status: " . $conn->error;
        $_SESSION['tipo'] = "error";
    }
    
    $stmt->close();
    $conn->close();
    
    header("Location: ../gerenciamento.php");
    exit();
}
?>