<?php
header("Content-Type: application/json");
require_once "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

foreach($data as $item){
    $sql = 'UPDATE videojuegos 
            SET existencia = existencia - $1 
            WHERE id_videojuego = $2';
    pg_query_params($conexion, $sql, array($item["cantidad"], $item["id"]));
}

echo json_encode(["success" => true]);
?>
