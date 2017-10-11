<?php

namespace app;

/**
 * Class Request
 * @package app
 */
class Request
{

    public $uri; // request uri
    public $width; // image width
    public $height; // image height
    public $mode; // image resize mode
    public $host; // host
    public $path; // image path
    public $filename; // image file name
    public $extension; // image extension

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->_checkExtension();

        $tmp = explode('/', $this->uri);
        array_shift($tmp);
        array_shift($tmp);

        list($this->width, $this->height, $this->mode) = explode('x', $tmp[0]);

        array_shift($tmp);
        $this->host = array_shift($tmp);
        $this->filename = array_pop($tmp);
        $this->path = implode('/', $tmp);
    }

    /**
     * @throws \Exception
     */
    private function _checkExtension()
    {
        $this->extension = strtolower(pathinfo($this->uri, PATHINFO_EXTENSION));
        if (!in_array($this->extension, [
            'png', 'jpg', 'jpeg'
        ])) {
            throw new \Exception("Extension not allowed", 400);
        }
    }


}