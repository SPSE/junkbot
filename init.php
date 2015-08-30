<?php

require(__DIR__ . '/vendor/autoload.php');

use junkbot\JunkBot;

$config = require(__DIR__ . '/conf.php');

$apiToken = $config['apiToken'];

$bot = new JunkBot($apiToken);
$bot->poll();