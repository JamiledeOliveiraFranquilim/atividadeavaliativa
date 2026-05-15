<?php
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'];
    $descricao = $_POST['descricao'];
    $setor = $_POST['setor'];
    $prioridade = $_POST['prioridade'];
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("INSERT INTO tarefas (usuario_id, descricao, setor, prioridade, status) VALUES (?, ?, ?, ?, 'a_fazer')");
    $stmt->bind_param("isss", $usuario_id, $descricao, $setor, $prioridade);
    
    if ($stmt->execute()) {
        setMensagem("Tarefa cadastrada com sucesso!", "success");
    } else {
        setMensagem("Erro ao cadastrar tarefa: " . $conn->error, "error");
    }
    
    $stmt->close();
    $conn->close();
    
    header("Location: cadastro_tarefa.php");
    exit();
} else {
    header("Location: cadastro_tarefa.php");
    exit();
}
?>