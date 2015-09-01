<?php

namespace junkbot;

use junkbot\core\TelegramPollingBot;
use junkbot\core\OpenExchangeCurrency;

/*
 * Junkbot, the bot that does all kinds of junk.
 */
class JunkBot extends TelegramPollingBot {

    // Currency convertion handler
    private $currency;

    public function __construct($telegramApiToken, $currencyApiToken) {
        parent::__construct($telegramApiToken);
        $this->currency = new OpenExchangeCurrency($currencyApiToken);
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
}
