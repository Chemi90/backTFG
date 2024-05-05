<?php
include 'response_template.php';

try {
    include 'db_connect.php';
    // Recibir los datos por JSON
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    // Verificar si se recibieron los datos necesarios
    if ($data === null || !isset($data['cuerpoMensaje']) || !isset($data['perfilIAID']) || !isset($data['usuarioID'])) {
        $respuesta['error'] = 'Faltan datos necesarios para el envío del mensaje';
        echo json_encode($respuesta);
        exit;
    }

    // Asignar datos a variables
    $cuerpoMensaje = $data['cuerpoMensaje'];
    $perfilIAID = $data['perfilIAID'];
    $usuarioID = $data['usuarioID'];

    // Preparar la sentencia SQL para insertar el nuevo mensaje
    $sql = "INSERT INTO MensajeEnviado (CuerpoMensaje, FechaGuardado, PerfilIAID, UsuarioID) VALUES (?, CURRENT_TIMESTAMP, ?, ?)";

    if ($stmt = mysqli_prepare($con, $sql)) {
        // Vincular parámetros para marcadores
        mysqli_stmt_bind_param($stmt, "sii", $cuerpoMensaje, $perfilIAID, $usuarioID);
        
        // Ejecutar la sentencia
        if(mysqli_stmt_execute($stmt)) {
            $respuesta['success'] = true;
            $respuesta['mensaje'] = 'Mensaje enviado guardado con éxito';
        } else {
            $respuesta['error'] = 'Error al guardar el mensaje enviado';
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