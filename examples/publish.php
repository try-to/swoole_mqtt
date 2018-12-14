<?php

use TrytoMqtt\Client;

require_once __DIR__ . '/../vendor/autoload.php';

$options = [
    'clean_session' => false,
    'client_id' => 'demo-publish-123456',
    'username' => '',
    'password' => '',
];

$mqtt = new Client('192.168.1.5', 1883, $options);

$mqtt->onConnect = function ($mqtt) {

    $mqtt->publish('/World', 'hello swoole mqtt');
};

$mqtt->onError = function ($exception) {
    echo "error\n";
};

$mqtt->onClose = function () {
    echo "close\n";
};

$mqtt->connect();
