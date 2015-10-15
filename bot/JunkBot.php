<?php

namespace junkbot;

use junkbot\core\GoogleTimezone;
use junkbot\core\TelegramPollingBot;
use junkbot\core\OpenExchangeCurrency;

/*
 * Junkbot, the bot that does all kinds of junk.
 */
class JunkBot extends TelegramPollingBot {

    // Currency conversion handler
    private $currency;
    // Time conversion handler
    private $time;

    public function __construct($telegramApiToken, $currencyApiToken, $timezoneApiToken) {
        parent::__construct($telegramApiToken);
        $this->currency = new OpenExchangeCurrency($currencyApiToken);
        $this->time = new GoogleTimezone($timezoneApiToken);
    }

    /*
     * Start command
     */
    protected function command_start() {
        // TODO: add bot description
        return "Welcome to Junkbot.\nPlease enjoy the say!";
    }

    /*
     * Help command
     */
    protected function command_help() {
        // TODO: add command help here
        // TODO: describe available commands
        return <<< EOT
            Available commands:

            /currency <amount> <currency_from> <currency_to>
            /help
            /time <location>
            /time <location to> -w <time_from> <location from>
            /time <location to> when <time_from> <location from>

            Time format: 10am, 10:00am, 10 am
EOT;
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
        return "Try /currency <amount> <currency_from> <currency_to>";
    }

    /*
     * Time conversion command
     * Expected input:
     * - <location>
     * - <location to> -w <time_from> <location from>
     * - <location to> when <time_from> <location from>
     * Time format: 10am, 10:00am, 10 am,
     */
    protected function command_time($args) {
        if (preg_match('/^([\-a-z ]+)(?: (?:-w|when) ([0-9]{1,2}(?: ?am|pm)|(?:[0-9]{1,2}:[0-9]{2})(?:am|pm)?) ([a-z\- ]+))?$/i', $args, $matches)) {
            $to = $matches[1];
            // Convert if 3 args are set, otherwise - get
            if (isset($matches[2])) {
                $time = $matches[2];
                $from = $matches[3];
                return $this->time->convert($time, $from, $to);
            }
            return $this->time->getTime($to);
        }
        return "Try: \n\t/time <location> \n\t/time <location to> -w <time> <location from> \n\t/time <location to> when <time> <location from>";
    }
}
