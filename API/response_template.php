<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');


// Respuesta por defecto
$respuesta = [
    'success' => false,
    'data' => [],
    'error' => ''
];
?>