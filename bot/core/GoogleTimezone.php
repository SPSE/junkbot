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

    /*
     * Get time in a location.
     * Get coordinates of location, then get timezone offset for those coordinates.
     * Return current time+offset
     */
    public function getTime($location) {
        $geo = $this->callCached([$this, 'getGeo'], $location);
        if ($geo) {
            $timezoneOffset = $this->callCached([$this, 'getTimezoneOffset'], [$geo['lat'], $geo['lng']]);
            $time = date('h:i a', time() + $timezoneOffset);
            $address = $geo['address'];
            return "Time is $time in $address";
        }
        return "Invalid location";
    }

    /*
     * Convert time from one location to another.
     * Get coordinates of both locations, then get timezone offsets for those coordinates.
     * Return current time - offset from + offset to (time difference)
     */
    public function convert($timeFrom, $from, $to) {
        $timestamp = strtotime($timeFrom);
        $geoFrom = $this->callCached([$this, 'getGeo'], $from);
        $geoTo = $this->callCached([$this, 'getGeo'], $to);
        if ($geoTo && $geoFrom) {
            $timezoneOffsetFrom = $this->callCached([$this, 'getTimezoneOffset'], [$geoFrom['lat'], $geoFrom['lng'], $timestamp]);
            $timezoneOffsetTo = $this->callCached([$this, 'getTimezoneOffset'], [$geoTo['lat'], $geoTo['lng'], $timestamp]);
            $timeTo = date('h:i a', $timestamp - $timezoneOffsetFrom + $timezoneOffsetTo);

            $addressFrom = $geoFrom['address'];
            $addressTo = $geoTo['address'];
            return "Time is $timeTo in $addressTo when it is $timeFrom in $addressFrom";
        }
        return "Invalid location";
    }


    /*
     * Get geo coordinates for a location
     */
    private function getGeo($location) {
        $endpoint = '/geocode/json';
        $params = [
            'address' => urldecode($location),
            'key' => $this->token
        ];
        $resp = $this->getJson($endpoint, $params)['results'];

        // Check if location was found
        if (isset($resp[0])) {
            $lat = $resp[0]['geometry']['location']['lat'];
            $lng = $resp[0]['geometry']['location']['lng'];
            $address = $resp[0]['formatted_address'];
            return ['lat' => $lat, 'lng' => $lng, 'address' => $address];
        }
        return false;
    }

    /*
     * Get time offset by geo coordinates
     */
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
