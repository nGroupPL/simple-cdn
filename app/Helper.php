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

}