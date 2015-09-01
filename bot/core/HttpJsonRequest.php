<?php

namespace junkbot\core;

use Requests;
use Exception;

/*
 * Trait for working with HTTP requests that use JSON.
 */
trait HttpJsonRequest {
    // TODO: Either don't throw exceptions on status codes, or handle exceptions somewhere.

    private $apiBase;

    /*
     * HTTP GET wrapper.
     * Builds url with params e.g. ?id=xxx
     * Handles response codes
     * Returns parsed JSON
     */
    public function getJson($endpoint, $params=null) {
        $url = $this->apiBase . $endpoint;

        // Build url query from params array
        if (isset($params)) {
            $url .= '?' . http_build_query($params);
        }
        $headers = ['Accept' => 'application/json'];

        $resp = Requests::get($url, $headers);

        if ($resp->status_code == 401) {
            throw new Exception('Invalid access token provided');
        }
        else if ($resp->status_code != 200) {
            throw new Exception("ERROR: Got status code $resp->status_code");
        }
        return json_decode($resp->body, true);
    }

    /*
     * HTTP POST wrapper.
     * Handles response codes
     * Returns parsed JSON
     */
    public function postJson($endpoint, $data) {
        $url = $this->apiBase . $endpoint;
        $headers = ['Accept' => 'application/json'];

        $resp = Requests::post($url, $headers, $data);

        if ($resp->status_code == 401) {
            throw new Exception('Invalid access token provided');
        }
        else if ($resp->status_code != 200) {
            throw new Exception("ERROR: Got status code $resp->status_code");
        }
        return json_decode($resp->body, true);
    }
}
