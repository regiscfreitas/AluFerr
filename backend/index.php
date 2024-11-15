<?php
// Configurações de CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

// Configurações de conexão com o banco de dados PostgreSQL
$host = 'localhost';
$port = '5432';
$dbname = 'projetoIntegradoSchema';
$user = 'postgres';
$password = 'regis';

$array = [
    'error' => '',
    'result' => []
];

// String de conexão
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
$conn = pg_connect($conn_string);

if (!$conn) {
    die("Erro ao conectar ao banco de dados");
}


