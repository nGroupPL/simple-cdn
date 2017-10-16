<?php


namespace app;

use app\providers\BaseProvider;
use Imagine\Image\Box;
use Imagine\Gd\Imagine;

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
        $this->resized_file = ROOT . '/public/' . ltrim($this->request->uri, '/');
        Helper::mkdir(pathinfo($this->resized_file, PATHINFO_DIRNAME));

        $image = (new Imagine())->open($this->original_file);
        $image->resize(new Box($this->request->width, $this->request->height));
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

        throw new \Exception("Forbidden", 403);
    }

    /**
     * Prepare request
     */
    private function _request()
    {
        $this->request = new Request();
    }

    /**
     * Init spl auto loader
     */
    private function _loader()
    {
        spl_autoload_register(function ($class) {
            include ROOT . '/' . str_replace('\\', '/', $class) . '.php';
        });
    }
}