<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

$conn = getConnection();

$query = "
    SELECT 
        t.*,
        u.nome as usuario_nome,
        u.setor as usuario_setor
    FROM tarefas t
    JOIN usuarios u ON t.usuario_id = u.id
    ORDER BY 
        FIELD(t.prioridade, 'alta', 'media', 'baixa'),
        t.data_cadastro DESC
";

$result = $conn->query($query);
$tarefas = [];

while ($row = $result->fetch_assoc()) {
    $tarefas[] = $row;
}

echo json_encode([
    'success' => true,
    'tarefas' => $tarefas
]);

$conn->close();
?>