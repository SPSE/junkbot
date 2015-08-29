<?php

namespace junkbot\core;

use Requests;
use Exception;

/*
 * Base class for implementing https://core.telegram.org/bots/api
 */
abstract class TelegramBot {

    const HOST = 'api.telegram.org';
    const PORT = 443;

    private $apiBase;
    private $token;

    public $botName;

    private $offset;

    public function __construct($token) {
        $this->token = $token;
        $this->apiBase = 'https://' . TelegramBot::HOST . ':' . TelegramBot::PORT . '/bot' . $token;
        $this->getMe();
    }

    public function poll() {
        while(true) {
            $params = [];
            if ($this->offset) {
                $params['offset'] = $this->offset;
            }

            $response = $this->get('getUpdates', $params);
            foreach ($response as $update) {
                $this->offset = $update['update_id'] + 1;
                $this->processMessage($update['message']);
            }
            sleep(2);
        }
    }

    /*
     * GET wrapper
     */
    private function get($apiMethod, $params = []) {
        $url = $this->apiBase . '/' . $apiMethod;

        if ($params) {
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

        return json_decode($resp->body, true)['result'];
    }

    /*
     * POST wrapper
     */
    private function post($apiMethod, $data) {
        $url = $this->apiBase . '/' . $apiMethod;
        $headers = ['Accept' => 'application/json'];

        $resp = Requests::post($url, $headers, $data);

        if ($resp->status_code == 401) {
            throw new Exception('Invalid access token provided');
        }
        else if ($resp->status_code != 200) {
            throw new Exception("ERROR: Got status code $resp->status_code");
        }

        return json_decode($resp->body, true)['result'];
    }

    private function getMe() {
        $data = $this->get('getMe');
        $this->botName = $data['username'];
    }

    public function setWebhook($url) {
        $this->post('setWebhook', ['url' => $url]);
    }

    private function sendMessage($chat_id, $text) {
        $this->post('sendMessage', ['chat_id' => $chat_id, 'text' => $text]);
    }

    public function processMessage($message) {
        // TODO: process system messages
        // TODO: make handler for bot commands
        $chat_id = $message['chat']['id'];

        // Loopback for testing
        $text = 'TEST:' . $message['text'];
        $this->sendMessage($chat_id, $text);
    }
}