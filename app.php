<?php

// Habilita CORS - En producción, reemplaza '*' por tu dominio específico
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

set_time_limit(500);

// Lee el cuerpo de la solicitud POST que contiene JSON
$data = json_decode(file_get_contents("php://input"), true);

$systemContent = $data['systemContent'] ?? 'Default system content'; // Proporciona un valor por defecto
$userContent = $data['userContent'] ?? 'Default user content'; // Proporciona un valor por defecto

// Configura los datos para enviar a la API
$postData = json_encode([
    "messages" => [
        [
            "role" => "system",
            "content" => $systemContent
        ],
        [
            "role" => "user",
            "content" => $userContent
        ]
    ],
    "model" => "mixtral-8x7b-32768"
]);

// Inicializa cURL
$ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer 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'
]);

// Ejecuta la petición
$result = curl_exec($ch);

// Cierra el recurso cURL
curl_close($ch);

if ($result === false) {
    echo json_encode(["error" => "Error al procesar tu solicitud."]);
    http_response_code(500);
} else {
    $resultArray = json_decode($result, true);

    // Verifica si la estructura esperada está presente en la respuesta
    if (isset($resultArray['choices'][0]['message']['content'])) {
        $responseText = $resultArray['choices'][0]['message']['content'];
        echo json_encode(["response" => $responseText]);
    } else {
        // Si la estructura esperada no está presente, devuelve un error y la respuesta completa para depuración
        echo json_encode([
            "error" => "La respuesta no tiene el formato esperado.",
            "debug" => $resultArray
        ]);
        http_response_code(500);
    }
}
?>
