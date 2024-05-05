<?php
include 'response_template.php';

function customErrorLogger($message) {
    $file = 'my_error_log.txt';
    $date = date('Y-m-d H:i:s');
    file_put_contents($file, "[$date] $message\n", FILE_APPEND);
}

try {
    include 'db_connect.php';
    customErrorLogger("Conexión establecida con éxito.");

    // Preparar la sentencia SQL para seleccionar todos los perfiles de IA
    $sql = "SELECT * FROM PerfilIA";
    customErrorLogger("Preparando consulta SQL: " . $sql);

    if ($result = mysqli_query($con, $sql)) {
        $perfiles = [];
        customErrorLogger("Consulta SQL ejecutada con éxito.");

        // Recorrer los resultados y añadirlos al array de perfiles
        while ($fila = mysqli_fetch_assoc($result)) {
            $perfiles[] = $fila;
            customErrorLogger("Perfil añadido: " . json_encode($fila));
        }

        if (!empty($perfiles)) {
            $respuesta['success'] = true;
            $respuesta['data'] = $perfiles;
        } else {
            $respuesta['error'] = 'No se encontraron perfiles de IA';
            $respuesta['success'] = false;
            customErrorLogger("No se encontraron perfiles de IA.");
        }

        // Liberar el conjunto de resultados
        mysqli_free_result($result);
    } else {
        $error = mysqli_error($con);
        $respuesta['error'] = 'Error al realizar la consulta: ' . $error;
        $respuesta['success'] = false;
        customErrorLogger("Error en la consulta SQL: " . $error);
    }

    // Cerrar la conexión
    mysqli_close($con);
} catch (Exception $e) {
    $respuesta['error'] = $e->getMessage();
    $respuesta['success'] = false;
    customErrorLogger("Excepción capturada: " . $e->getMessage());
} finally {
    echo json_encode($respuesta);
    customErrorLogger("Respuesta enviada: " . json_encode($respuesta));
}
?>
