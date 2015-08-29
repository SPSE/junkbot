<?php

require(__DIR__ . '/vendor/autoload.php');

use junkbot\JunkBot;

$config = require(__DIR__ . '/conf.php');

$webhookUrl = $config['webhookUrl'];
$apiToken = $config['apiToken'];

$bot = new JunkBot($apiToken, $webhookUrl);
$bot->poll();

// TODO: make webhooks work
//$bot->setWebhook($webhookUrl);
//$input = file_get_contents('php://input');
//$data = json_decode($input, true);
//$bot->processMessage($data['message']);
