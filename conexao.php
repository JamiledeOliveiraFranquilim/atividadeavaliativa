<?php
session_start();

$host = 'localhost';
$dbname = 'atividadejamile';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Erro na conexão: ' . $e->getMessage()]));
}

// Função para verificar se usuário está logado
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: pages/login.php');
        exit;
    }
}

// Função para verificar se é admin
function verificarAdmin() {
    verificarLogin();
    if ($_SESSION['usuario_tipo'] !== 'admin') {
        header('Location: index.php');
        exit;
    }
}
?>