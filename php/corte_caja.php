<?php
// php/corte_caja.php
header('Content-Type: application/json; charset=utf-8');

// evita que se muestren warnings antes del JSON
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require_once "conexion.php";
date_default_timezone_set("America/Mexico_City");

// obtener y validar fecha
$fecha = $_GET['fecha'] ?? date("Y-m-d");
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    http_response_code(400);
    echo json_encode(["error" => "Formato de fecha inválido. Use YYYY-MM-DD."]);
    exit;
}

// consulta segura con parametro tipo date
$sql = "SELECT 
            p.id_pedido,
            p.fecha_pedido,
            p.total,
            p.estado,
            p.id_videojuego,
            v.titulo AS nombre_videojuego
        FROM pedidos p
        LEFT JOIN videojuegos v ON v.id_videojuego = p.id_videojuego
        WHERE DATE(p.fecha_pedido) = $1::date
        ORDER BY p.fecha_pedido ASC";

$result = pg_query_params($conexion, $sql, [$fecha]);

if ($result === false) {
    http_response_code(500);
    echo json_encode(["error" => pg_last_error($conexion) ?: "Error desconocido en la base de datos."]);
    pg_close($conexion);
    exit;
}

$pedidos = [];
$total_dia = 0.0;
while ($row = pg_fetch_assoc($result)) {
    // asegurarse que total sea numérico
    $row['total'] = isset($row['total']) ? (float)$row['total'] : 0.0;
    $total_dia += $row['total'];
    $pedidos[] = $row;
}

echo json_encode([
    "fecha" => $fecha,
    "total_dia" => $total_dia,
    "pedidos" => $pedidos
]);

pg_close($conexion);
