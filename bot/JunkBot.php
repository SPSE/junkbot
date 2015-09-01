<?php

namespace junkbot;

use junkbot\core\GoogleTimezone;
use junkbot\core\TelegramPollingBot;
use junkbot\core\OpenExchangeCurrency;

/*
 * Junkbot, the bot that does all kinds of junk.
 */
class JunkBot extends TelegramPollingBot {

    // Currency convertion handler
    private $currency;
    // Time conversion handler
    private $time;

    public function __construct($telegramApiToken, $currencyApiToken, $timezoneApiToken) {
        parent::__construct($telegramApiToken);
        $this->currency = new OpenExchangeCurrency($currencyApiToken);
        $this->time = new GoogleTimezone($timezoneApiToken);
    }

    /*
     * Help command
     */
    protected function command_help() {
        // TODO: add command help here
        return "Welcome to Junkbot.\nPlease enjoy the say!";
    }

    /*
     * Currency conversion command
     * Expected input: "<amount> <currency_from> <currency_to>"
     */
    protected function command_currency($args) {
        if (preg_match('/^([0-9]*?) ([A-Za-z]{3}) ([A-Za-z]{3})$/i', $args, $matches)) {
            $amount = intval($matches[1]);
            $from = strtoupper($matches[2]);
            $to = strtoupper($matches[3]);
            return $this->currency->convert($amount, $from, $to);
        }
        else
        {
            return 'Try /currency <amount> <currency_from> <currency_to>';
        }
    }

    protected function command_time($args) {
        // TODO: define command format for convert
        // <loc> <time> ?
        //$timestamp = strtotime($time);
        //$this->time->convert($timestamp, $location);
        return $this->time->getTime($args);
    }
}
