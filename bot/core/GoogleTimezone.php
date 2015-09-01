<?php

namespace junkbot\core;

/*
 * Implements time calculating based on Google Geocoding and Timezone API
 * https://developers.google.com/maps/documentation/geocoding/intro
 * https://developers.google.com/maps/documentation/timezone/intro
 */
class GoogleTimezone {
    use Cacheable;
    use HttpJsonRequest;

    private $token;

    public function __construct($token) {
        $this->token = $token;
        $this->apiBase = 'https://maps.googleapis.com/maps/api';
    }

    public function getTime($location) {
        $geo = $this->callCached([$this, 'getGeo'], $location);
        $timezoneOffset = $this->callCached([$this, 'getTimezoneOffset'], [$geo['lat'], $geo['lng']]);
        $time = date('h:i a', time() + $timezoneOffset);
        $address = $geo['address'];
        return "Time is $time at $address";
    }

    public function convert($timestamp, $location) {
        $geo = $this->callCached([$this, 'getGeo'], $location);
        $timezoneOffset = $this->callCached([$this, 'getTimezoneOffset'], [$geo['lat'], $geo['lng'], $timestamp]);

        // TODO: convert
    }

    private function getGeo($location) {
        $endpoint = '/geocode/json';
        $params = [
            'address' => urldecode($location),
            'key' => $this->token
        ];
        $resp = $this->getJson($endpoint, $params)['results'][0];

        $lat = $resp['geometry']['location']['lat'];
        $lng = $resp['geometry']['location']['lng'];
        $address = $resp['formatted_address'];
        return ['lat' => $lat, 'lng' => $lng, 'address' => $address];
    }

    private function getTimezoneOffset($lat, $lng, $timestamp=null) {
        $endpoint = '/timezone/json';
        $params = [
            'location' => $lat . ',' . $lng,
            'key' => $this->token,
            'timestamp' => isset($timestamp) ? $timestamp : time()
        ];
        $timezoneData = $this->getJson($endpoint, $params);
        $offset = $timezoneData['rawOffset'] + $timezoneData['dstOffset'];
        return $offset;
    }
}
