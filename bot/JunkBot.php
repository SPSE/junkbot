<?php

namespace junkbot;

use junkbot\core\TelegramPollingBot;
use junkbot\core\OpenExchangeCurrency;


class JunkBot extends TelegramPollingBot {
    // TODO: more useful functions here
    // Think about worker queue to process requests, e.g. for timer

    private $currency;

    public function __construct($telegramApiToken, $currencyApiToken) {
        parent::__construct($telegramApiToken);
        $this->currency = new OpenExchangeCurrency($currencyApiToken);
    }

    protected function command_help() {
        return "Welcome to Junkbot.\nPlease enjoy the say!";
    }

    protected function command_currency($args) {
        if (preg_match('/^([0-9]*?) ([A-Za-z]{3}) ([A-Za-z]{3})$/i', $args, $matches)) {

            $amount = intval($matches[1]);
            $from = strtoupper($matches[2]);
            $to = strtoupper($matches[3]);
            return $this->currency->convert($amount, $from, $to);
        }
        else
        {
            return 'Try /currency <amount> <first currency> <second_currency>';
        }
    }
}
