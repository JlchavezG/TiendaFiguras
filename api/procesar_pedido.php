<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/db.php';

$database = new Database();
$db = $database->getConnection();

// Obtener datos del carrito desde el cuerpo de la solicitud
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['items']) || empty($input['items'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Carrito vacío']);
    exit;
}

try {
    $db->beginTransaction();

    // Calcular total
    $total = 0;
    foreach ($input['items'] as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }

    // Insertar pedido
    $query_pedido = "INSERT INTO pedidos (total) VALUES (:total)";
    $stmt_pedido = $db->prepare($query_pedido);
    $stmt_pedido->bindParam(':total', $total);
    $stmt_pedido->execute();
    $pedido_id = $db->lastInsertId();

    // Insertar items del pedido
    $query_item = "INSERT INTO items_pedido (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (:pedido_id, :producto_id, :cantidad, :precio_unitario, :subtotal)";
    $stmt_item = $db->prepare($query_item);

    foreach ($input['items'] as $item) {
        $subtotal = $item['precio'] * $item['cantidad'];
        $stmt_item->bindParam(':pedido_id', $pedido_id);
        $stmt_item->bindParam(':producto_id', $item['id']);
        $stmt_item->bindParam(':cantidad', $item['cantidad']);
        $stmt_item->bindParam(':precio_unitario', $item['precio']);
        $stmt_item->bindParam(':subtotal', $subtotal);
        $stmt_item->execute();
    }

    $db->commit();

    // Generar ticket
    $ticket = [
        'numero_pedido' => $pedido_id,
        'fecha' => date('Y-m-d H:i:s'),
        'items' => $input['items'],
        'total' => $total
    ];

    echo json_encode([
        'success' => true,
        'mensaje' => 'Pedido procesado exitosamente',
        'ticket' => $ticket
    ]);

} catch (Exception $e) {
    $db->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Error al procesar el pedido: ' . $e->getMessage()]);
}
?>