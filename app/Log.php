<?php

namespace app;


class Log
{

    private static $handle;

    public static function init($file)
    {
        if (!is_file($file)) {
            $path = dirname($file);
            if (!is_writeable($path)) {
                exit("Log path ($path) is not writeable");
            }
            touch($file);
        }

        static::$handle = fopen($file, 'a');
        register_shutdown_function(['app\Log', 'shutdown']);
    }

    public static function log($message)
    {
        fwrite(static::$handle, "[" . date("d/M/Y H:i:s") . "] " . $message . "\n");
    }

    public static function shutdown()
    {
        fclose(static::$handle);
    }

}