<?php
$servername = "localhost";
$username = "root";     // Alterado de "usuario" para "root"
$password = "";         // Alterado de "senha" para vazio (sem senha)
$dbname = "atividadeJamile";

$conexao = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Comente ou remova esta linha depois de testar
// echo "Conexão realizada com sucesso!";
?>