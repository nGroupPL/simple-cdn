<?php
/**
 * Created by IntelliJ IDEA.
 * User: piotrek
 * Date: 29.10.17
 * Time: 17:22
 */

namespace app\processors;


use app\Helper;

/**
 * Image with text generator
 *
 * Class ImgGenProcessor
 * @package app\processors
 */
class ImgGenProcessor extends BaseProcessor
{
    /**
     * @throws \Exception
     */
    public function run()
    {
        if (count($this->uri_parts) < 6) {
            throw new \Exception("Not enough parameters");
        }

        list($size, $bg_color, $fg_color, $font, $font_size, $text) = $this->uri_parts;

        $size = explode('x', $size);
        if (count($size) != 2) {
            throw new \Exception("Invalid size");
        }
        list($width, $height) = $size;


        $text = pathinfo($text, PATHINFO_FILENAME);
        $text = urldecode($text);

        $width = filter_var($width, FILTER_VALIDATE_INT);
        $height = filter_var($height, FILTER_VALIDATE_INT);
        $font_size = filter_var($font_size, FILTER_VALIDATE_INT);
        $font = preg_replace("/[^A-Za-z0-9_]/", '', $font);

        if (empty($width) || empty($height) || $width < 0 || $height < 0) {
            throw new \Exception("Invalid call");
        }

        if (empty($font_size)) {
            // @todo we need better algorithm for this
            $font_size = $width - 30 * strlen($text);
            if ($font_size <= 0) {
                $font_size = 5;
            }
        }

        $file = ROOT . '/public/' . ltrim($this->app->uri, '/');
        Helper::mkdir(pathinfo($file, PATHINFO_DIRNAME));

        empty($font) && $font = 'ariblk';

        $font_file = ROOT . '/vendor/caarlos0-graveyard/msfonts/fonts/' . $font . '.ttf';

        if (!file_exists($font_file)) {
            throw new \Exception("Font don't exists", 500);
        }

        $type_space = imagettfbbox($font_size, 0, $font_file, $text);

        $text_width = abs($type_space[4] - $type_space[0]);
        $text_height = abs($type_space[5] - $type_space[1]);

        $image = imagecreatetruecolor($width, $height);

        list ($r, $g, $b) = Helper::hex2rgb($fg_color);
        $text_color = imagecolorallocate($image, $r, $g, $b);

        if (empty($bg_color)) {
            imagealphablending($image, false);
            $transparency = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparency);
            imagesavealpha($image, true);
        } else {
            list ($r, $g, $b) = Helper::hex2rgb($bg_color);
            $bg_color = imagecolorallocate($image, $r, $g, $b);
            imagefill($image, 0, 0, $bg_color);
        }

        $x = ($width - $text_width) / 2;
        $y = ($height + $text_height) / 2;

        imagettftext($image, $font_size, 0, $x, $y, $text_color, $font_file, $text);

        header('Content-type: image/png');
        imagepng($image, $file);

        imagedestroy($image);

        Helper::output($file, 'png');
    }

}