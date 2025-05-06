<?php
require '__init__.php';
require 'Base64Converter.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$usuario = DB::queryFirstRow("SELECT img, nome FROM users WHERE id = %i", $id);

if ($usuario && isset($usuario['img'])) {
    $resizedBase64Image = Base64Converter::resizeBase64Image($usuario['img'], 50, 50);

    $binaryImage = Base64Converter::toBinary($resizedBase64Image);

    header("Content-Type: image/png");
    echo $binaryImage;
    exit;
}

http_response_code(404);
echo "Imagem não encontrada.";