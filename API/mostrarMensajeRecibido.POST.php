<?php
include 'response_template.php';

try {
    include 'db_connect.php';
    
    // Recibir los datos por JSON
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    // Verificar si se recibieron los datos necesarios
    if ($data === null || !isset($data['perfilIAID']) || !isset($data['usuarioID'])) {
        $respuesta['error'] = 'Faltan datos necesarios para la consulta';
        echo json_encode($respuesta);
        exit;
    }

    $perfilIAID = $data['perfilIAID'];
    $usuarioID = $data['usuarioID'];

    // Preparar la sentencia SQL para seleccionar los mensajes recibidos
    $sql = "SELECT MensajeRecibidoID, CuerpoMensaje, FechaGuardado FROM MensajeRecibido WHERE PerfilIAID = ? AND UsuarioID = ?";

    if ($stmt = mysqli_prepare($con, $sql)) {
        // Vincular los parámetros
        mysqli_stmt_bind_param($stmt, "ii", $perfilIAID, $usuarioID);

        // Ejecutar la sentencia
        mysqli_stmt_execute($stmt);

        // Vincular el resultado a variables
        mysqli_stmt_bind_result($stmt, $MensajeRecibidoID, $CuerpoMensaje, $FechaGuardado);

        // Inicializar array para guardar los resultados
        $mensajesRecibidos = [];

        // Recolectar los resultados
        while (mysqli_stmt_fetch($stmt)) {
            $mensajesRecibidos[] = [
                'MensajeRecibidoID' => $MensajeRecibidoID,
                'CuerpoMensaje' => $CuerpoMensaje,
                'FechaGuardado' => $FechaGuardado
            ];
        }

        if (!empty($mensajesRecibidos)) {
            $respuesta['success'] = true;
            $respuesta['data'] = $mensajesRecibidos;
        } else {
            $respuesta['error'] = 'No se encontraron mensajes recibidos';
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
