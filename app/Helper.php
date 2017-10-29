<?php

namespace app;

/**
 * Class Helper
 *
 * @package app
 */
class Helper
{

    /**
     * Recursive create directory
     *
     * @param $path
     * @throws \Exception
     */
    public static function mkdir($path)
    {
        $path = explode('/', ltrim($path, '/'));

        $p = '';
        foreach ($path as $d) {
            $p .= '/' . $d;

            if (!is_dir($p)) {
                if (!mkdir($p)) {
                    throw new \Exception("Can't make dir: $p");
                }
            }
        }
    }

    /**
     * Output file to browser with specific header
     *
     * @param $filename
     * @param $extension
     */
    public static function output($filename, $extension)
    {
        switch ($extension) {
            case "gif":
                $type = "image/gif";
                break;
            case "png":
                $type = "image/png";
                break;
            case "jpeg":
            case "jpg":
            default:
                $type = "image/jpeg";
                break;
        }

        header('Content-type: ' . $type);
        echo file_get_contents($filename);
    }


    /**
     * Convert hex color to rgb
     *
     * @param $hex
     * @return array
     * @throws \Exception
     */
    public static function hex2rgb($hex): array
    {
        $hex = str_replace("#", "", $hex);
        $l = strlen($hex);
        if ($l == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } elseif ($l == 6) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        } else {
            throw new \Exception("Invalid color", 500);
        }

        return [$r, $g, $b];
    }
}