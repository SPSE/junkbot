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
        // Time to keep rates in cache
        $this->keepTime = 3600;
    }

    /*
     * Convert currency
     */
    public function convert($amount, $from, $to) {
        // Get rates from cache, if no valid cache exists rates are fetched from API
        $rates =  $this->callCached([$this, 'getRates']);

        // Check if received currency names exist
        if (isset($rates[$from]) && isset($rates[$to])){

            /*
             * Calculate converted amount
             * Since rates use base USD currency we first convert <from> to USD, then USD to <to>
             */
            $result = round($amount / $rates[$from] * $rates[$to], 4);
            return "$amount $from is $result $to";
        }
        return "Invalid currency name";
    }

    /*
     * Get current currency rates from the API
     */
    private function getRates() {
        $endpoint = '/latest.json';
        $params = ['app_id' => $this->token];
        return $this->getJson($endpoint, $params)['rates'];
    }
}
