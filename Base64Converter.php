<?php

class Base64Converter
{
    public static function toBinary($base64String)
    {
        if (strpos($base64String, 'base64,') !== false) {
            $base64String = substr($base64String, strpos($base64String, 'base64,') + 7);
        }
        return base64_decode($base64String);
    }

    public static function resizeBase64Image($base64String, $maxWidth, $maxHeight, $quality = 75)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $base64Data = substr($base64String, strpos($base64String, ',') + 1);
            $mimeType = strtolower($type[1]);
        } else {
            throw new Exception('Formato base64 inválido.');
        }

        $imageData = base64_decode($base64Data);
        $sourceImage = imagecreatefromstring($imageData);
        if (!$sourceImage) {
            throw new Exception('Não foi possível criar imagem a partir da base64.');
        }

        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);
        $scale = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = floor($width * $scale);
        $newHeight = floor($height * $scale);

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        if (in_array($mimeType, ['png', 'gif'])) {
            imagecolortransparent($resizedImage, imagecolorallocatealpha($resizedImage, 0, 0, 0, 127));
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
        }

        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();
        switch ($mimeType) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($resizedImage, null, $quality);
                $outputMime = 'image/jpeg';
                break;
            case 'png':
                imagepng($resizedImage, null, 6);
                $outputMime = 'image/png';
                break;
            case 'gif':
                imagegif($resizedImage);
                $outputMime = 'image/gif';
                break;
            default:
                throw new Exception('Tipo de imagem não suportado: ' . $mimeType);
        }
        $resizedData = ob_get_clean();

        return 'data:' . $outputMime . ';base64,' . base64_encode($resizedData);
    }

    public static function saveBase64ToFile($base64String, $filePath)
    {
        $binaryData = self::toBinary($base64String);
        return file_put_contents($filePath, $binaryData);
    }
}
