<?php
namespace App\Services\Yandex;

/**
 * Class Response
 * @package Yandex\Geo
 * @license The MIT License (MIT)
 */
class Response extends \Yandex\Geo\Response
{
    /**
     * Широта в градусах. Имеет десятичное представление с точностью до семи знаков после запятой
     * @return float|null
     */
    public function getLatitude()
    {
        $result = null;
        if (isset($this->_data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'])) {
            list(,$latitude) = explode(' ', $this->_data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos']);
            $result = (float)$latitude;
        }
        return $result;
    }

    /**
     * Долгота в градусах. Имеет десятичное представление с точностью до семи знаков после запятой
     * @return float|null
     */
    public function getLongitude()
    {
        $result = null;
        if (isset($this->_data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'])) {
            list($longitude,) = explode(' ', $this->_data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos']);
            $result = (float)$longitude;
        }
        return $result;
    }
}
