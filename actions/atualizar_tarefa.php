<?php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $usuario_id = $_POST['usuario_id'];
    $descricao = $_POST['descricao'];
    $setor = $_POST['setor'];
    $prioridade = $_POST['prioridade'];
    $status = $_POST['status'];
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("UPDATE tarefas SET usuario_id = ?, descricao = ?, setor = ?, prioridade = ?, status = ? WHERE id = ?");
    $stmt->bind_param("issssi", $usuario_id, $descricao, $setor, $prioridade, $status, $id);
    
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Tarefa atualizada com sucesso!";
        $_SESSION['tipo'] = "success";
    } else {
        $_SESSION['mensagem'] = "Erro ao atualizar tarefa: " . $conn->error;
        $_SESSION['tipo'] = "error";
    }
    
    $stmt->close();
    $conn->close();
    
    header("Location: ../gerenciamento.php");
    exit();
}
?>