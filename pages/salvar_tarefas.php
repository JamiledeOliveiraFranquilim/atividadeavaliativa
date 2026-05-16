<?php
session_start();
require_once '../conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$tarefas = $data['tarefas'] ?? [];

if (empty($tarefas)) {
    echo json_encode(['success' => false, 'message' => 'Nenhuma tarefa para cadastrar']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$cadastradas = 0;

try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("INSERT INTO tarefas (usuario_id, descricao, setor, prioridade, status) VALUES (?, ?, ?, ?, 'a_fazer')");
    
    foreach ($tarefas as $tarefa) {
        $stmt->execute([
            $usuario_id,
            $tarefa['descricao'],
            $tarefa['setor'],
            $tarefa['prioridade']
        ]);
        $cadastradas++;
    }
    
    $pdo->commit();
    
    echo json_encode(['success' => true, 'total' => $cadastradas]);
    
} catch(PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>