<?php

namespace junkbot;

use junkbot\core\TelegramPollingBot;


class JunkBot extends TelegramPollingBot {

    // TODO: useful functions here
    // Think about worker queue to process requests, e.g. for timer
    // Cache stuff

    protected function command_help() {
        return "Welcome to Junkbot.\nPlease enjoy the say!";
    }

    protected function command_time() {
        return date('m/d/Y h:i:s a');
    }
}

