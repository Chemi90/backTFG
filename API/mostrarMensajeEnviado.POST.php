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

    // Preparar la sentencia SQL para seleccionar los mensajes enviados
    $sql = "SELECT MensajeEnviadoID, CuerpoMensaje, DATE_ADD(FechaGuardado, INTERVAL 6 HOUR) AS FechaGuardado FROM MensajeEnviado WHERE PerfilIAID = ? AND UsuarioID = ? ORDER BY FechaGuardado";

    if ($stmt = mysqli_prepare($con, $sql)) {
        // Vincular los parámetros
        mysqli_stmt_bind_param($stmt, "ii", $perfilIAID, $usuarioID);

        // Ejecutar la sentencia
        mysqli_stmt_execute($stmt);

        // Vincular el resultado a variables
        mysqli_stmt_bind_result($stmt, $MensajeEnviadoID, $CuerpoMensaje, $FechaGuardado);

        // Inicializar array para guardar los resultados
        $mensajesEnviados = [];

        // Recolectar los resultados
        while (mysqli_stmt_fetch($stmt)) {
            $mensajesEnviados[] = [
                'MensajeEnviadoID' => $MensajeEnviadoID,
                'CuerpoMensaje' => $CuerpoMensaje,
                'FechaGuardado' => $FechaGuardado
            ];
        }

        if (!empty($mensajesEnviados)) {
            $respuesta['success'] = true;
            $respuesta['data'] = $mensajesEnviados;
        } else {
            $respuesta['error'] = 'No se encontraron mensajes enviados';
        }

        // Cerrar la sentencia
        mysqli_stmt_close($stmt);
    } else {
        $error = mysqli_error($con);
        $respuesta['error'] = 'Error al realizar la consulta: ' . $error;
    }

    // Cerrar la conexión
    mysqli_close($con);
} catch (Exception $e) {
    $respuesta['error'] = $e->getMessage();
} finally {
    echo json_encode($respuesta);
}
?>
