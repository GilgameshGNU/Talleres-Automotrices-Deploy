<?php
// Test login via POST
$data = ['login' => 'admin@empresa.com', 'contrasena' => 'Admin@2024'];
$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\nAccept: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true
    ]
];
$context = stream_context_create($options);
$result = file_get_contents('http://127.0.0.1:8000/api/login', false, $context);
echo "=== LOGIN CON EMAIL ===\n";
echo $result . "\n";
echo "Headers: " . $http_response_header[0] . "\n\n";

// Test login with username
$data2 = ['login' => 'admin', 'contrasena' => 'Admin@2024'];
$options2 = [
    'http' => [
        'header'  => "Content-type: application/json\r\nAccept: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data2),
        'ignore_errors' => true
    ]
];
$context2 = stream_context_create($options2);
$result2 = file_get_contents('http://127.0.0.1:8000/api/login', false, $context2);
echo "=== LOGIN CON NOMBRE DE USUARIO ===\n";
echo $result2 . "\n";
echo "Headers: " . $http_response_header[0] . "\n";
