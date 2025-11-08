<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/db.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT id, nombre, descripcion, precio, imagen FROM productos WHERE stock > 0";
$stmt = $db->prepare($query);
$stmt->execute();

$productos = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $productos[] = $row;
}

echo json_encode($productos);
?>