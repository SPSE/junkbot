<?php

require(__DIR__ . '/vendor/autoload.php');

use junkbot\JunkBot;

$config = require(__DIR__ . '/conf.php');

// TODO: move API tokens somewhere else
$telegramApiToken = $config['telegramApiToken'];
$currencyApiToken = $config['currencyApiToken'];
$timezoneApiToken = $config['timezoneApiToken'];

$bot = new JunkBot($telegramApiToken, $currencyApiToken, $timezoneApiToken);

echo "Bot is running.";
$bot->poll();