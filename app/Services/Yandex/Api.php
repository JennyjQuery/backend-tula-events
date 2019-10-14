<?php
namespace App\Services\Yandex;

/**
 * Class Api
 * @package Yandex\Geo
 * @license The MIT License (MIT)
 * @see http://api.yandex.ru/maps/doc/geocoder/desc/concepts/About.xml
 */
class Api extends \Yandex\Geo\Api
{
    public function load(array $options = [])
    {
        $apiUrl = sprintf('https://geocode-maps.yandex.ru/%s/?%s', $this->_version, http_build_query($this->_filters));
        $curl = curl_init($apiUrl);
        $options += array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPGET => 1,
            CURLOPT_FOLLOWLOCATION => 1,
        );
        curl_setopt_array($curl, $options);
        $data = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new \Yandex\Geo\Exception\CurlError($error);
        }
        curl_close($curl);
        if (in_array($code, array(500, 502))) {
            $msg = strip_tags($data);
            throw new \Yandex\Geo\Exception\ServerError(trim($msg), $code);
        }
        $data = json_decode($data, true);
        if (empty($data)) {
            $msg = sprintf('Can\'t load data by url: %s', $apiUrl);
            throw new \Yandex\Geo\Exception($msg);
        }
        if (!empty($data['error'])) {
            throw new \Yandex\Geo\Exception\MapsError($data['error']['message'], $data['error']['code']);
        }


        $this->_response = new Response($data);

        return $this;
    }
}
