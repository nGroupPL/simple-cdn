<?php
/**
 * Created by IntelliJ IDEA.
 * User: piotrek
 * Date: 29.10.17
 * Time: 16:43
 */

namespace app\processors;


use app\Cdn;

abstract class BaseProcessor
{
    public $app;
    public $uri_parts;

    public function __construct(Cdn $app, array $parts)
    {
        $this->app = $app;
        $this->uri_parts = $parts;
        $this->run();
    }

    abstract public function run();


}