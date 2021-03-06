<?php

namespace junkbot\core;

/*
 * Base class for implementing https://core.telegram.org/bots/api
 */
abstract class TelegramPollingBot {
    // TODO: add more API function handling
    // TODO: handle system messages

    use HttpJsonRequest;

    const HOST = 'api.telegram.org';
    const PORT = 443;
    const POLL_TIMEOUT = 30;

    private $apiBase;
    private $token;
    private $offset;

    public $botName;

    public function __construct($token) {
        $this->token = $token;
        $this->apiBase = 'https://' . TelegramPollingBot::HOST . ':' . TelegramPollingBot::PORT . '/bot' . $token;
        $this->getMe();
    }

    /*
     * GET wrapper
     */
    private function get($apiMethod, $params = []) {
        $endpoint = '/' . $apiMethod;
        return $this->getJson($endpoint, $params)['result'];
    }

    /*
     * POST wrapper
     */
    private function post($apiMethod, $data) {
        $endpoint = '/' . $apiMethod;
        return $this->postJson($endpoint, $data)['result'];
    }

    /*
     * Test connectivity by fetching bot's username
     */
    private function getMe() {
        $data = $this->get('getMe');
        $this->botName = $data['username'];
    }

    /*
     * Send text to target chat_id
     */
    private function sendMessage($chat_id, $text) {
        $this->post('sendMessage', ['chat_id' => $chat_id, 'text' => $text]);
    }

    /*
     * Start long polling loop to fetch messages from server
     */
    public function poll() {
        while(true) {
            $params = [];
            $params['timeout'] = TelegramPollingBot::POLL_TIMEOUT;
            // Set offset so we don't fetch old messages repeatedly
            if ($this->offset) {
                $params['offset'] = $this->offset;
            }

            $response = $this->get('getUpdates', $params);
            foreach ($response as $update) {
                $this->offset = $update['update_id'] + 1;
                $this->processMessage($update['message']);
            }
        }
    }

    /*
     * Process received message
     */
    public function processMessage($message) {
        if(isset($message['text'])) {
            $chat_id = $message['chat']['id'];
            $text = $message['text'];

            /*
             * Parse received message.
             * Expected format: /<some_command> <command_text>
             * Output: "some_command", "command_text"
             */
            if (preg_match('/^\/(?:([a-z0-9]+)(?:(?:[ ]+)(.+?))?)?$/i', $text, $matches)) {
                $command = 'command_' . $matches[1];
                $args = isset($matches[2]) ? $matches[2] : null;
                $this->runCommand($command, $chat_id, $args);
            }
        }
    }

    /*
     * Lookup if received command exists and run it. Reply with data received from command.
     */
    private function runCommand($command, $chat_id, $args)
    {
        if (method_exists($this, $command)) {
            $resp = $this->$command($args);
            $this->sendMessage($chat_id, $resp);
        }
    }

    // TODO: define some basic functions that must be implemented
    abstract protected function command_help();
}
