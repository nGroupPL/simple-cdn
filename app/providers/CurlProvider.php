<?php

namespace app\providers;


use app\Log;
use app\Request;

/**
 * Class CurlProvider
 * Allow retrieve file from endpoint with curl php extension
 *
 * @package app\providers
 */
class CurlProvider extends BaseProvider
{

    /**
     *
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function retrieve(Request $request): string
    {

        $url = "https://" . $request->host . '/api/v1/cdn?file='
            . base64_encode($request->path . '/' . $request->filename)
            . '&token=' . $this->config['token'];;

        Log::log("Retrieve: " . $url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if (DEV_MODE) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $result = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code != 200) {
            Log::log("Curl error: " . $http_code);
            Log::log(curl_error($ch));
            throw new \Exception("File not found", 404);
        }

        curl_close($ch);

        Log::log("File retrieved, size: " . curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD));

        return $result;

    }
}