<?php

namespace junkbot\core;

use Requests;
use Exception;

trait HttpJsonRequest {

    private $apiBase;

    public function getJson($endpoint, $params=null) {
        $url = $this->apiBase . $endpoint;
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
