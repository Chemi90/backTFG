<?php
include 'response_template.php';

try {
    include 'db_connect.php';
    // Recibimos los datos por JSON
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if ($data === null || !isset($data['correoElectronico']) || !isset($data['contrasena'])) {
        $respuesta['error'] = 'Error en los datos recibidos';
        echo json_encode($respuesta);
        exit;
    }
    
    // Y luego, cuando preparas y ejecutas la sentencia SQL
    $usuarioLogeado = $data['correoElectronico'];
    $usuarioClave = md5($data['contrasena']); // Asegúrate de que la lógica de hash coincida con la del registro    

    $sql = "SELECT UsuarioID, Nombre FROM Usuario WHERE CorreoElectronico = ? AND ContrasenaEncriptada = ?";

    // Preparar y ejecutar la sentencia
    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $usuarioLogeado, $usuarioClave);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $idUsuario, $nombreUsuario);
        $result = mysqli_stmt_fetch($stmt);

        if ($result) {
            $respuesta['success'] = true;
            $respuesta['data'] = ['id_usuario' => $idUsuario, 'nombre' => $nombreUsuario];
        } else {
            $respuesta['error'] = 'Usuario o clave incorrecta';
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