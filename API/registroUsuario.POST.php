<?php
include 'response_template.php';

try {
    include 'db_connect.php';
    // Recibir los datos por JSON
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    // Verificar si se recibieron los datos necesarios
    if ($data === null || !isset($data['nombre']) || !isset($data['correoElectronico']) || !isset($data['clave'])) {
        $respuesta['error'] = 'Faltan datos necesarios para el registro';
        echo json_encode($respuesta);
        exit;
    }

    // Asignar datos a variables
    $nombre = $data['nombre'];
    $correoElectronico = $data['correoElectronico'];
    $clave = md5($data['clave']); // Encriptar la contraseña con MD5

    // Preparar la sentencia SQL para insertar el nuevo usuario
    $sql = "INSERT INTO Usuario (Nombre, CorreoElectronico, ContrasenaEncriptada) VALUES (?, ?, ?)";

    if ($stmt = mysqli_prepare($con, $sql)) {
        // Vincular parámetros para marcadores
        mysqli_stmt_bind_param($stmt, "sss", $nombre, $correoElectronico, $clave);
        
        // Ejecutar la sentencia
        if(mysqli_stmt_execute($stmt)) {
            $respuesta['success'] = true;
            $respuesta['mensaje'] = 'Usuario registrado con éxito';
        } else {
            // Verificar si el error es por un correo electrónico duplicado
            if(mysqli_errno($con) == 1062) {
                $respuesta['error'] = 'El correo electrónico ya está registrado';
            } else {
                $respuesta['error'] = 'Error al registrar el usuario';
            }
        }
        // Cerrar la sentencia
        mysqli_stmt_close($stmt);
    } else {
        $respuesta['error'] = 'Error al preparar la consulta: ' . mysqli_error($con);
    }

    // Cerrar la conexión
    mysqli_close($con);
} catch (Exception $e) {
    $respuesta['error'] = $e->getMessage();
} finally {
    echo json_encode($respuesta);
}
?>