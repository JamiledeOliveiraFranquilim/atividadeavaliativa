<?php
session_start();

// Conexão com o banco
$host = 'localhost';
$dbname = 'atividadejamile';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no banco: ' . $e->getMessage()]);
    exit;
}

header('Content-Type: application/json');

// Verificar login
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

// Pegar dados do POST
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

$status_validos = ['a_fazer', 'fazendo', 'concluido'];

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

if (!in_array($status, $status_validos)) {
    echo json_encode(['success' => false, 'message' => 'Status inválido']);
    exit;
}

try {
    // Verificar permissão
    $stmt = $pdo->prepare("SELECT usuario_id FROM tarefas WHERE id = ?");
    $stmt->execute([$id]);
    $tarefa = $stmt->fetch();
    
    if (!$tarefa) {
        echo json_encode(['success' => false, 'message' => 'Tarefa não encontrada']);
        exit;
    }
    
    if ($_SESSION['usuario_tipo'] != 'admin' && $tarefa['usuario_id'] != $_SESSION['usuario_id']) {
        echo json_encode(['success' => false, 'message' => 'Sem permissão']);
        exit;
    }
    
    // Atualizar status
    $stmt = $pdo->prepare("UPDATE tarefas SET status = ? WHERE id = ?");
    if ($stmt->execute([$status, $id])) {
        echo json_encode(['success' => true, 'message' => 'Atualizado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>