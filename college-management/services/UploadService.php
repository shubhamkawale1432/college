<?php
declare(strict_types=1);

namespace Services;

final class UploadService
{
    private static array $allowedImageMimes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'image/svg+xml' => 'svg',
    ];

    private static array $allowedDocMimes = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public static function saveImage(array $file, string $subfolder = 'avatars', int $maxBytes = 5242880): ?string
    {
        return self::processUpload($file, $subfolder, self::$allowedImageMimes, $maxBytes);
    }

    public static function saveDocument(array $file, string $subfolder = 'complaints', int $maxBytes = 10485760): ?string
    {
        return self::processUpload($file, $subfolder, self::$allowedDocMimes, $maxBytes);
    }

    private static function processUpload(array $file, string $subfolder, array $allowedMimes, int $maxBytes): ?string
    {
        if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > $maxBytes) {
            throw new \RuntimeException('File size exceeds the permitted limit.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!isset($allowedMimes[$mime])) {
            throw new \RuntimeException('Invalid file type. Only standard images and PDF documents are allowed.');
        }

        $ext = $allowedMimes[$mime];
        $safeName = $subfolder . '_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;

        $targetDir = dirname(__DIR__) . '/storage/uploads/' . trim($subfolder, '/');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $targetPath = $targetDir . '/' . $safeName;
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \RuntimeException('Failed to save uploaded file.');
        }

        return trim($subfolder, '/') . '/' . $safeName;
    }
}
