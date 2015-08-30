<?php

namespace junkbot\core;

use Requests;
use Exception;


/*
 * Implements currency conversion via https://openexchangerates.org
 */
class OpenExchangeCurrency {
    use Cacheable;

    private $apiBase = 'https://openexchangerates.org/api/';
    private $token;

    public function __construct($token) {
        $this->token = $token;
    }

    public function convert($amount, $from, $to) {
        $rates =  $this->callCached([$this, 'getRates']);

        if (isset($rates[$from]) && isset($rates[$to])){
            $result = round($amount / $rates[$from] * $rates[$to], 2);
            return "$amount $from is $result $to";
        }
        return "Invalid currency name";
    }

    private function getRates() {
        $url = $this->apiBase . 'latest.json?app_id=' . $this->token;
        $headers = ['Accept' => 'application/json'];

        // TODO: refactor not to duplicate code in bot. Maybe move to trait
        $resp = Requests::get($url, $headers);

        if ($resp->status_code == 401) {
            throw new Exception('Invalid access token provided');
        }
        else if ($resp->status_code != 200) {
            throw new Exception("ERROR: Got status code $resp->status_code");
        }
        return json_decode($resp->body, true)['rates'];
    }

}