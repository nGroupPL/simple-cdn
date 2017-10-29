<?php

namespace app\providers;


use app\Helper;
use app\Log;
use app\processors\BaseProcessor;
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
     * @param BaseProcessor $processor
     * @return string
     */
    public function getFile(BaseProcessor $processor): string
    {
        $filename = ROOT . '/public/img/' . $processor->host . '/' . $processor->path . '/' . $processor->filename;

        Log::log("Obtaining file: " . $filename);

        if (!file_exists($filename)) {
            $data = $this->retrieve($processor);
            Helper::mkdir(pathinfo($filename, PATHINFO_DIRNAME));
            file_put_contents($filename, $data);
        }

        return $filename;

    }

    abstract public function retrieve(BaseProcessor $processor): string;
}