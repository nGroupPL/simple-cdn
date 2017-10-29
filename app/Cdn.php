<?php


namespace app;

use app\providers\BaseProvider;

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

    public $uri; // request uri
    public $parts = [];
    public $processor;

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
    }

    /**
     * Prepare request
     */
    private function _request()
    {
        $this->uri = $_SERVER['REQUEST_URI'];

        $tmp = explode('/', ltrim($this->uri, '/'));
        $processor = array_shift($tmp);

        $className =
            'app\\processors\\' .
            str_replace(' ', '', ucwords(preg_replace('/[^A-Za-z0-9]+/', ' ', $processor)))
            . 'Processor';

        $this->processor = new $className($this, $tmp);
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