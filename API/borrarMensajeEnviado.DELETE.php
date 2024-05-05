<?php
include 'response_template.php';

try {
    include 'db_connect.php';

    // Verificar si se recibió el ID del mensaje enviado como parámetro de URL
    if (!isset($_GET['id'])) {
        $respuesta['error'] = 'Falta el ID del mensaje enviado para realizar la operación';
        echo json_encode($respuesta);
        exit;
    }

    // Asignar el ID del mensaje enviado a una variable
    $id = $_GET['id'];

    // Preparar la sentencia SQL para borrar el mensaje enviado
    $sql = "DELETE FROM MensajeEnviado WHERE MensajeEnviadoID = ?";

    if ($stmt = mysqli_prepare($con, $sql)) {
        // Vincular el parámetro
        mysqli_stmt_bind_param($stmt, "i", $id);

        // Ejecutar la sentencia
        if (mysqli_stmt_execute($stmt)) {
            // Verificar si algún registro fue afectado
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $respuesta['success'] = true;
                $respuesta['mensaje'] = 'Mensaje enviado borrado con éxito';
            } else {
                $respuesta['error'] = 'El mensaje enviado no fue encontrado o ya fue borrado';
            }
        } else {
            $respuesta['error'] = 'Error al borrar el mensaje enviado';
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
