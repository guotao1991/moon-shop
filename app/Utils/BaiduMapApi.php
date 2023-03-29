<?php

namespace App\Utils;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Throwable;

class BaiduMapApi
{
    private const AK = 'Gr6wDUyqC0viMC5vOPBznpdLdoTaLiym';

    /**
     * 根据地址获取经纬度
     * @param string $address 地址
     * @param string $city 城市
     * @return string
     */
    public static function getGeoCoderByAddress($address, $city = '')
    {
        $address    = urlencode($address);
        $city       = urldecode($city);
        $url = "http://api.map.baidu.com/geocoding/v3/?address={$address}&output=json&city={$city}&ak=" . self::AK;

        $client = new Client();
        $result = null;
        try {
            $result = $client->request("GET", $url);
            $result = json_decode($result, 1);
        } catch (Throwable $e) {
            return "";
        }

        if (!empty($result) && isset($result['status']) && $result['status'] == 'OK') {
            return $result['result']['location'];
        }

        return "";
    }

    /**
     * 根据经纬度获取地址
     * @param  string $lat 经度
     * @param  string $lng 纬度
     * @return array
     */
    public static function getGeoCoder($lat, $lng)
    {
        $url = "http://api.map.baidu.com/geocoder?location=$lat,$lng&output=json&key=" . self::AK;
        $client = new Client();
        $result = [];
        try {
            $result = $client->request("GET", $url);
            $result = json_decode($result, 1);
        } catch (Throwable $e) {
            return [];
        }

        if (!empty($result) && isset($result['status']) && $result['status'] == 'OK') {
            return $result;
        }
        return [];
    }
}
