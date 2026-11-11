<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

/**
 * Turns an uploaded picture into a share-card image: 1200x630 (the 1.91:1 ratio Facebook, WhatsApp,
 * LinkedIn and X all crop to) and small enough for WhatsApp, which drops thumbnails over a few hundred KB.
 */
class OgImage
{
    public const WIDTH = 1200;

    public const HEIGHT = 630;

    /** WhatsApp is the strictest crawler: keep the file under this many bytes. */
    public const MAX_BYTES = 300 * 1024;

    /** Returns a JPEG upload ready to store, or the original file when it cannot be decoded (no GD, SVG, corrupt). */
    public static function prepare(UploadedFile $file): UploadedFile
    {
        if (! function_exists('imagecreatetruecolor') || ! ($bytes = @file_get_contents($file->getRealPath()))) {
            return $file;
        }

        $source = @imagecreatefromstring($bytes);
        if (! $source) {
            return $file;
        }

        $sw = imagesx($source);
        $sh = imagesy($source);

        // Cover-crop around the centre so the card is always filled.
        $scale = max(self::WIDTH / $sw, self::HEIGHT / $sh);
        $cw = (int) round(self::WIDTH / $scale);
        $ch = (int) round(self::HEIGHT / $scale);

        $canvas = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255)); // JPEG has no alpha
        imagecopyresampled($canvas, $source, 0, 0, intdiv($sw - $cw, 2), intdiv($sh - $ch, 2), self::WIDTH, self::HEIGHT, $cw, $ch);

        $jpeg = '';
        foreach ([85, 78, 70, 60, 50, 40] as $quality) {
            ob_start();
            imagejpeg($canvas, null, $quality);
            $jpeg = (string) ob_get_clean();

            if (strlen($jpeg) <= self::MAX_BYTES) {
                break;
            }
        }

        $path = tempnam(sys_get_temp_dir(), 'og');
        file_put_contents($path, $jpeg);

        return new UploadedFile($path, 'og-image.jpg', 'image/jpeg', null, true);
    }
}
