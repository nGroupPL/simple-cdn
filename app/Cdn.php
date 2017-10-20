<?php


namespace app;

use app\providers\BaseProvider;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

/**
 * Class Cdn
 * @package app
 */
class Cdn
{
    /**
     * @var array
     */
    public $config;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var BaseProvider
     */
    public $provider;

    /**
     * @var string original size file path
     */
    public $original_file;

    /**
     * @var string resized file path
     */
    public $resized_file;

    /**
     * @param $config
     */
    public function run(array $config)
    {
        $this->config = $config;
        $this->_loader();
        $this->_logger();
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
        Helper::output($this->resized_file, $this->request->extension);
    }

    /**
     * Resize image file
     */
    private function _resize()
    {
        Log::log("try to resize: {$this->original_file} > {$this->request->uri}");
        $this->resized_file = ROOT . '/public/' . ltrim($this->request->uri, '/');
        Helper::mkdir(pathinfo($this->resized_file, PATHINFO_DIRNAME));

        $width = $this->request->width;
        $height = $this->request->height;

        $image = (new Imagine())->open($this->original_file);

        switch ($this->request->mode) {

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
        $this->original_file = $this->provider->getFile($this->request);
    }

    /**
     * Get and check host
     * @throws \Exception
     */
    private function _host()
    {
        foreach ($this->config['hosts'] as $host => $config) {

            if ($host == $this->request->host
                || ($host[0] == '*' && strpos($this->request->host, substr($host, 1)))) {

                $this->provider = new $config['provider']($config);

                return;
            }
        }

        Log::log("Host {$this->request->host} not allowed");
        throw new \Exception("Forbidden", 403);
    }

    /**
     * Prepare request
     */
    private function _request()
    {
        $this->request = new Request();
    }

    private function _logger()
    {
        Log::init(dirname(__DIR__) . '/app.log');
    }

    /**
     * Init spl auto loader
     */
    private function _loader()
    {
        spl_autoload_register(function ($class) {
            $file = ROOT . '/' . str_replace('\\', '/', $class) . '.php';
            if (!file_exists($file)) {
                exit("file ($file) for class $class not exists");
            }
            include $file;
        });
    }
}