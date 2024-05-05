<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include 'db_connect.php';
include 'response_template.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['nombre']) && !empty($data['promptSystem']) && !empty($data['descripcion'])) {
    $nombre = $data['nombre'];
    $promptSystem = $data['promptSystem'];
    $descripcion = $data['descripcion'];

    $sql = "INSERT INTO PerfilIA (nombre, PromptSystem, descripcion) VALUES (?, ?, ?)";
    
    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $nombre, $promptSystem, $descripcion);
        
        if(mysqli_stmt_execute($stmt)) {
            $respuesta['success'] = true;
            $respuesta['message'] = 'Perfil creado exitosamente.';
        } else {
            $respuesta['error'] = 'Error al crear el perfil: ' . mysqli_error($con);
        }
        mysqli_stmt_close($stmt);
    } else {
        $respuesta['error'] = 'Error al preparar la consulta: ' . mysqli_error($con);
    }
} else {
    $respuesta['error'] = 'Datos incompletos.';
}

echo json_encode($respuesta);
mysqli_close($con);
?>
