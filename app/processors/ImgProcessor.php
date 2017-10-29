<?php

namespace app\processors;

use app\Helper;
use app\Log;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;


/**
 * Created by IntelliJ IDEA.
 * User: piotrek
 * Date: 29.10.17
 * Time: 16:17
 */
class ImgProcessor extends BaseProcessor
{

    public $width; // image width
    public $height; // image height
    public $mode; // image resize mode
    public $host; // host
    public $path; // image path
    public $filename; // image file name
    public $extension;
    private $provider;
    private $original_file;
    private $resized_file; // image extension

    /**
     * @internal param $config
     */
    public function run()
    {
        $this->_checkExtension();
        $this->_request();
        $this->_host();
        $this->_obtainFile();
        $this->_resize();
        $this->_output();
    }


    /**
     * Output processed file to browser
     */
    private function _output()
    {
        Helper::output($this->resized_file, $this->extension);
    }

    /**
     * Resize image file
     */
    private function _resize()
    {
        Log::log("try to resize: {$this->original_file} > {$this->app->uri}");
        $this->resized_file = ROOT . '/public/' . ltrim($this->app->uri, '/');
        Helper::mkdir(pathinfo($this->resized_file, PATHINFO_DIRNAME));

        $width = $this->width;
        $height = $this->height;

        $image = (new Imagine())->open($this->original_file);

        switch ($this->mode) {

            case 1:
                $image->resize(new Box($width, $height));
                break;
            case 2:

                $size = new Box($width, $height);
                $mode = ImageInterface::THUMBNAIL_INSET;
                $image = $image->thumbnail($size, $mode);
                $sizeR = $image->getSize();

                $tmp = (new Imagine())->create($size, (new RGB())->color('#ffffff', 0));
                $startX = $startY = 0;
                if ($sizeR->getWidth() < $width) {
                    $startX = ($width - $sizeR->getWidth()) / 2;
                }
                if ($sizeR->getHeight() < $height) {
                    $startY = ($height - $sizeR->getHeight()) / 2;
                }
                $tmp->paste($image, new Point($startX, $startY));
                $image = $tmp;
                break;
            case 3:
                $size = new Box($width, $height);
                $mode = ImageInterface::THUMBNAIL_INSET;
                $image = $image->thumbnail($size, $mode);
                break;
            case 4:
                $size = new Box($width, $height);
                $mode = ImageInterface::THUMBNAIL_OUTBOUND;
                $image = $image->thumbnail($size, $mode);
                break;

        }

        $image->save($this->resized_file);
    }

    /**
     * Obtain original file
     */
    private function _obtainFile()
    {
        $this->original_file = $this->provider->getFile($this);
    }

    private function _request()
    {
        list($this->width, $this->height, $this->mode) = explode('x', $this->uri_parts[0]);

        array_shift($this->uri_parts);
        $this->host = array_shift($this->uri_parts);
        $this->filename = array_pop($this->uri_parts);
        $this->path = implode('/', $this->uri_parts);
    }

    /**
     * Get and check host
     * @throws \Exception
     */
    private function _host()
    {
        foreach ($this->app->config['hosts'] as $host => $config) {

            if ($host == $this->host
                || ($host[0] == '*' && strpos($this->host, substr($host, 1)))) {

                $this->provider = new $config['provider']($config);

                return;
            }
        }

        Log::log("Host {$this->host} not allowed");
        throw new \Exception("Forbidden. Host {$this->host} not allowed", 403);
    }

    /**
     * @throws \Exception
     */
    private function _checkExtension()
    {
        $this->extension = strtolower(pathinfo($this->app->uri, PATHINFO_EXTENSION));
        if (!in_array($this->extension, [
            'png', 'jpg', 'jpeg'
        ])) {
            throw new \Exception("Extension not allowed", 400);
        }
    }
}