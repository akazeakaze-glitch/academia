<?php

$host = "mysql-d9ad40f-corpoeforma.h.aivencloud.com";
$user = "avnadmin";
$pass = "AVNS_UhJh7UVxiBLy-kWJEc_";
$db   = "defaultdb";
$port = 24506;

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("<h2 style='color:red;'>Erro de Conexão: " . $conn->connect_error . "</h2>");
}

$sql_file = __DIR__ . '/academia.sql';

if (!file_exists($sql_file)) {
    die("<h2 style='color:red;'>Erro: Cole o arquivo academia.sql na mesma pasta deste arquivo importar.php!</h2>");
}

$sql_content = file_get_contents($sql_file);

if ($conn->multi_query($sql_content)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    echo "<h1 style='color:green;'>SUCESSO! O banco de dados foi importado para a Aiven!</h1>";
} else {
    echo "<h2 style='color:red;'>Erro ao importar: " . $conn->error . "</h2>";
}

$conn->close();
?>
