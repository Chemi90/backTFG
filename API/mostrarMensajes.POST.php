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

    // Preparar la sentencia SQL para seleccionar los mensajes recibidos y enviados
    $sql = "(
        SELECT MensajeRecibidoID AS id, CuerpoMensaje AS texto, FechaGuardado AS fecha, 'recibido' AS tipo 
        FROM MensajeRecibido 
        WHERE PerfilIAID = ? AND UsuarioID = ?
    ) UNION ALL (
        SELECT MensajeEnviadoID AS id, CuerpoMensaje AS texto, DATE_ADD(FechaGuardado, INTERVAL 6 HOUR) AS fecha, 'enviado' AS tipo 
        FROM MensajeEnviado 
        WHERE PerfilIAID = ? AND UsuarioID = ?
    ) ORDER BY fecha";

    if ($stmt = mysqli_prepare($con, $sql)) {
        // Vincular los parámetros para ambas consultas
        mysqli_stmt_bind_param($stmt, "iiii", $perfilIAID, $usuarioID, $perfilIAID, $usuarioID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Inicializar array para guardar los resultados
        $mensajes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $mensajes[] = $row;
        }

        if (!empty($mensajes)) {
            $respuesta['success'] = true;
            $respuesta['data'] = $mensajes;
        } else {
            $respuesta['error'] = 'No se encontraron mensajes';
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
