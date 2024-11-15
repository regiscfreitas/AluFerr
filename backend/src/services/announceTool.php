<?php
require "../../index.php";

$method = strtolower($_SERVER['REQUEST_METHOD']);

if ($method === 'post') {
    // Debug: Imprime os dados recebidos
    error_log(print_r($_POST, true));
    error_log(print_r($_FILES, true));

    // Extraindo dados do POST
    $codproduto = $_POST['codproduto'] ?? null;
    $nomeproduto = $_POST['nomeproduto'] ?? null;
    $valordiaria = $_POST['valordiaria'] ?? null;
    $marca = $_POST['marca'] ?? null;
    $modelo = $_POST['modelo'] ?? null;
    $voltagem = $_POST['voltagem'] ?? null;
    $datacriacao = $_POST['datacriacao'] ?? null;

    // Processa a imagem
    $imagemproduto = null;
    if (isset($_FILES['imagemproduto']) && $_FILES['imagemproduto']['error'] === UPLOAD_ERR_OK) {
        // Lê o conteúdo do arquivo de imagem
        $imagemproduto = file_get_contents($_FILES['imagemproduto']['tmp_name']);

        // Converte para o formato correto para PostgreSQL
        $imagemproduto = pg_escape_bytea($imagemproduto);
    } else {
        error_log("Erro ao fazer upload da imagem: " . $_FILES['imagemproduto']['error']);
    }

    // Verifica se os valores estão definidos antes de fazer a inserção
    if ($codproduto && $nomeproduto && $valordiaria) {
        $query = "INSERT INTO produto (codproduto, nomeproduto, valordiaria, marca, modelo, voltagem, imagemproduto, datacriacao) 
                  VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
        $params = [$codproduto, $nomeproduto, $valordiaria, $marca, $modelo, $voltagem, $imagemproduto, $datacriacao];

        $result = pg_query_params($conn, $query, $params);

        if ($result) {
            $array['result'] = [
                'codproduto' => $codproduto,
                'nomeproduto' => $nomeproduto,
                'valordiaria' => $valordiaria,
                'marca' => $marca,
                'modelo' => $modelo,
                'voltagem' => $voltagem,
                'datacriacao' => $datacriacao,
                'imagemproduto' => base64_encode($imagemproduto)
            ];
        } else {
            $array['error'] = 'Erro ao salvar no banco de dados.';
        }
    } else {
        $array['error'] = 'Os campos codproduto, nomeproduto e valordiaria são obrigatórios.';
    }
} else {
    $array['error'] = 'Método não permitido (apenas POST)';
}

header('Content-Type: application/json');
echo json_encode($array);
