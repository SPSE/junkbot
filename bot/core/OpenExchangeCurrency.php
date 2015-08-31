<?php

namespace junkbot\core;

/*
 * Implements currency conversion via https://openexchangerates.org
 */
class OpenExchangeCurrency {
    use Cacheable;
    use HttpJsonRequest;

    private $token;

    public function __construct($token) {
        $this->token = $token;
        $this->apiBase = 'https://openexchangerates.org/api';
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
        $endpoint = '/latest.json';
        $params = ['app_id' => $this->token];
        return $this->getJson($endpoint, $params)['rates'];
    }
}
