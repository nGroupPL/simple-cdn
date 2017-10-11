<?php

namespace app\providers;


use app\Helper;
use app\Request;

/**
 * Class BaseProvider
 * @package app\providers
 */
abstract class BaseProvider
{
    /**
     * @var array Provider configuration
     */
    public $config;

    /**
     * BaseProvider constructor.
     * @param $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get original filename, if not exists retrieve it from endpoint
     *
     * @param Request $request
     * @return string
     */
    public function getFile(Request $request): string
    {

        $filename = ROOT . '/public/img/' . $request->host . '/' . $request->path . '/' . $request->filename;

        if (!file_exists($filename)) {
            $data = $this->retrieve($request);
            Helper::mkdir(pathinfo($filename, PATHINFO_DIRNAME));
            file_put_contents($filename, $data);
        }

        return $filename;

    }

    abstract public function retrieve(Request $request): string;
}