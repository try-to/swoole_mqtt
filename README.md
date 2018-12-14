# MQTT
Asynchronous MQTT client for PHP based on swoole.

# Installation
composer require try-to/swoole_mqtt

# 文档
[中文文档](#)

# Example
**subscribe.php**
```php
<?php
use TrytoMqtt\Client;
require_once __DIR__ . '/vendor/autoload.php';
$options = [
    'clean_session' => false,
    'client_id' => 'demo-subscribe-123456',
    'username' => '',
    'password' => '',
];
$mqtt = new Client('127.0.0.1', 1883, $options);
$mqtt->onConnect = function ($mqtt) {
    $mqtt->subscribe('/World');
};
$mqtt->onMessage = function ($topic, $content) {
    var_dump($topic, $content);
};
$mqtt->onError = function ($exception) use ($mqtt) {
    echo "error\n";
    // $mqtt->reconnect(1000);
};
$mqtt->onClose = function () {
    echo "close\n";
};
$mqtt->connect();
```
Run with command ```php subscribe.php```

**publish.php**
```php
<?php
use TrytoMqtt\Client;
require_once __DIR__ . '/../vendor/autoload.php';
$options = [
    'clean_session' => false,
    'client_id' => 'demo-publish-123456',
    'username' => '',
    'password' => '',
];
$mqtt = new Client('127.0.0.1', 1883, $options);
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
```

Run with command ```php publish.php```

## API

  * Client::__construct()
  * Client::connect()
  * Client::publish()
  * Client::subscribe()
  * Client::unsubscribe()
  * Client::disconnect()
  * Client::close()
  * callback onConnect
  * callback onMessage
  * callback onError
  * callback onClose

-------------------------------------------------------

### __construct (string $host, int $port, [array $options])

  * `$host` Service address. 
  * `$port` port.
  
  * `$options` is the client connection options. Defaults:
    * `keepalive`: `50` seconds, set to `0` to disable
    * `client_id`: client id, default `swoole-mqtt-client-{$mt_rand}`
    * `protocol_name`: `'MQTT'` or '`MQIsdp`'
    * `protocol_level`: `'MQTT'` is `4` and '`MQIsdp`' is `3`
    * `clean_session`: `true`, set to false to receive QoS 1 and 2 messages while
      offline
    * `reconnect_period`: `1` second, interval between two reconnections
    * `connect_timeout`: `30` senconds, time to wait before a CONNACK is received
    * `username`: the username required by your broker, if any
    * `password`: the password required by your broker, if any
    * `will`: a message that will sent by the broker automatically when
       the client disconnect badly. The format is:
      * `topic`: the topic to publish
      * `content`: the message to publish
      * `qos`: the QoS
      * `retain`: the retain flag
    * `resubscribe` : if connection is broken and reconnects,
       subscribed topics are automatically subscribed again (default `true`)
    * `bindto` default '', used to specify the IP address that PHP will use to access the network
    * `ssl` default `false`, it can be set `true` or `ssl context` see http://php.net/manual/en/context.ssl.php
    * `debug` default `false`, set `true` to show debug info

-------------------------------------------------------

### connect()

Connect to broker specified by the given $address and $options in `__construct($host, $port, $options)`.

-------------------------------------------------------

### publish(String $topic, String $content, [array $options], [callable $callback])

Publish a message to a topic

* `$topic` is the topic to publish to, `String`
* `$message` is the message to publish, `String`
* `$options` is the options to publish with, including:
  * `qos` QoS level, `Number`, default `0`
  * `retain` retain flag, `Boolean`, default `false`
  * `dup` mark as duplicate flag, `Boolean`, default `false`
* `$callback` - `function (\Exception $exception)`, fired when the QoS handling completes,
  or at the next tick if QoS 0. No error occurs then `$exception` will be null.
  
-------------------------------------------------------

### subscribe(mixed $topic, [array $options], [callable $callback])

Subscribe to a topic or topics

* `$topic` is a `String` topic or an `Array` which has as keys the topic name and as value
the QoS like `array('test1'=> 0, 'test2'=> 1)` to subscribe.
* `$options` is the options to subscribe with, including:
  * `qos` qos subscription level, default 0
* `$callback` - `function (\Exception $exception, array $granted)`
  callback fired on suback where:
  * `exception` a subscription error or an error that occurs when client is disconnecting
  * `granted` is an array of `array('topic' => 'qos', 'topic' => 'qos')` where:
    * `topic` is a subscribed to topic
    * `qos` is the granted qos level on it

-------------------------------------------------------

### unsubscribe(mixed $topic, [callable $callback])

Unsubscribe from a topic or topics

* `$topic` is a `String` topic or an array of topics to unsubscribe from
* `$callback` - `function (\Exception $e)`, fired on unsuback. No error occurs then `$exception` will be null..

-------------------------------------------------------

### disconnect()

Send DISCONNECT package to broker and close the client.

-------------------------------------------------------

### close()

Close the client without DISCONNECT package.

-------------------------------------------------------

### callback onConnect(Client $mqtt)
Emitted on successful connection (`CONNACK` package received).

-------------------------------------------------------

### callback onMessage(String $topic, String $content, Client $mqtt)
`function (topic, message, packet) {}`

Emitted when the client receives a publish packet
* `$topic` topic of the received packet
* `$content` payload of the received packet
* `$mqtt` Client instance.

-------------------------------------------------------

### callback onError(\Exception $exception)
Emitted when something wrong for example the client cannot connect broker.

-------------------------------------------------------

### callback onClose()
Emitted when connection closed.

-------------------------------------------------------


# License

MIT