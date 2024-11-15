<?php
require "../../index.php";


$result = pg_query($conn, "SELECT * FROM produto");
$data = pg_fetch_all($result);

if ($data) {
    // Map through the data to convert binary images into data URLs
    $data = array_map(function ($item) {
        // Check if imagemproduto is set and not null
        if (!empty($item['imagemproduto'])) {
            // First, unescape the bytea data
            $decodedImage = pg_unescape_bytea($item['imagemproduto']);

            // Then, convert the unescaped binary data to base64
            $item['imagemproduto'] = 'data:image/jpeg;base64,' . base64_encode($decodedImage);
        }
        return $item;
    }, $data);
}

echo json_encode($data);
