<?php
// Conexión a la base de datos con mysqli
$host = '13.51.171.187/127.0.0.1:3306'; // Tu servidor de Azure
$username = 'root'; // Usuario de Azure
$password = ''; // Contraseña
$dbname = 'josemigu_tfg'; // Nombre de la base de datos
$port = 3306; // Puerto

// Cadena de conexión a la base de datos PostgreSQL
$con = mysqli_connect($host, $username, $password, $dbname, $port);

// Conectarse a la base de datos PostgreSQL
if (!$con) {
    $respuesta['error'] = 'No se ha podido conectar con la base de datos: ' . mysqli_connect_error();
    echo json_encode($respuesta);
    exit;
}
?>