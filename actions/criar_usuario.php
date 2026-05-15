<?php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cargo = $_POST['cargo'];
    $setor = $_POST['setor'];
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, cargo, setor) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nome, $email, $cargo, $setor);
    
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Usuário cadastrado com sucesso!";
        $_SESSION['tipo'] = "success";
    } else {
        $_SESSION['mensagem'] = "Erro ao cadastrar usuário: " . $conn->error;
        $_SESSION['tipo'] = "error";
    }
    
    $stmt->close();
    $conn->close();
    
    header("Location: ../cadastro_usuario.php");
    exit();
}
?>