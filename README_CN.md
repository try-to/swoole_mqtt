# MQTT
一个基于swoole的异步mqtt 客户端库，可用于接收或者发送mqtt协议的消息。支持QoS 0、QoS 1、QoS 2。支持MQTT 3.1和3.1.1版本.

# 安装
composer require try-to/swoole_mqtt

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
命令行运行 ```php subscribe.php```  启动

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

命令行运行 ```php publish.php``` 启动

## 接口

  * Client::__construct()
  * Client::connect()
  * Client::reconnect()
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

创建一个mqtt客户端实例.

  * `$host` 服务地址. 
  * `$port` 端口.

  * `$options` 客户端选项数组，可以设置以下选项：
    * `keepalive`: 默认50秒，设置成0代表禁用
    * `client_id`: 客户端id，如果没设置默认是 "swoole-mqtt-client-".mt_rand()
    * `protocol_name`: 协议名，MQTT(3.1.1版本)或者 MQIsdp(3.1版本)，默认是MQTT
    * `protocol_level`: 协议等级， protocol_name是MQTT 时值为4 ，protocol_name是MQIsdp 时值是 3
    * `clean_session`: 清理会话，默认为true。设置为false可以接收到QoS 1和QoS 2级别的离线消息
    * `reconnect_period`: 重连时间间隔，默认 1 秒，0代表不重连
    * `connect_timeout`: 连接mqtt超时时间，默认30 秒
    * `username`: 用户名，可选
    * `password`: 密码，可选
    * `will`: 遗嘱消息，当客户端断线后Broker会自动发送遗嘱消息给其它客户端. 格式为:
       the client disconnect badly. The format is:
      * `topic`: 主题
      * `content`: 内容
      * `qos`: QoS等级
      * `retain`: retain标记
    * `resubscribe` : 当连接异常断开并重连后，是否重新订阅之前的主题，默认为true
    * `bindto` 用来指定本地以哪个ip和端口向Broker发起连接，默认值为' '
    * `ssl` ssl选项，默认是 false，如果设置为true，则以ssl方式连接。同时支持传入ssl上下文数组，用来配置本地证书等，ssl上下文参考 https://wiki.swoole.com/wiki/page/p-client_setting.html
    * `debug` 是否开启debug模式，debug模式可以输出与Broker通讯的详细信息，默认为false

-------------------------------------------------------

### connect()

连接服务

-------------------------------------------------------

### reconnect()

重新连接服务

-------------------------------------------------------

### publish(String $topic, String $content, [array $options], [callable $callback])

向某个主题发布一条消息

* `$topic` 主题
* `$message` 消息
* `$options` 选项数组，包括
  * `qos` QoS等级，默认0
  * `retain` retain 标记，默认false
  * `dup` 重发标志，默认false
* `$callback` 回调函数，当发生错误时或者发布成功时触发, 是异常对象，当没有错误发生时 $exception 为null，下同.
  
-------------------------------------------------------

### subscribe(mixed $topic, [array $options], [callable $callback])

订阅一个主题或者多个主题

* `$topic` 是一个字符串(订阅一个主题)或者数组(订阅多个主题)， 当订阅多个主题时，$topic是主题是key，QoS为值的数组，例如array('topic1'=> 0, 'topic2'=> 1)
* `$options` 订阅选项数组，包含以下设置:
  * `qos` QoS等级, 默认 0
* `$callback` -  回调函数，当订阅成功或者发生错误时触发：
  * `exception` 异常对象，无错误发生时它是null，下同
  * `granted` 订阅结果数组，类似 array('topic' => 'qos', 'topic' => 'qos') 其中:
    * `topic` 是订阅的主题
    * `qos` Broker接受的QoS等级

-------------------------------------------------------

### unsubscribe(mixed $topic, [callable $callback])

取消订阅

* `$topic` 是一个字符串或者字符串数组，类似array('topic1', 'topic2')
* `$callback` - `function (\Exception $e)`, 成功或者失败时触发的回调

-------------------------------------------------------

### disconnect()

正常断开与Broker的连接， DISCONNECT报文会被发送到Broker.

-------------------------------------------------------

### close()

强制断开与Broker的连接，不会发送DISCONNECT报文给Broker.

-------------------------------------------------------

### callback onConnect(Client $mqtt)
当与Broker连接建立完毕后触发。这时候已经收到了Broker的CONNACK 报文

-------------------------------------------------------

### callback onMessage(String $topic, String $content, Client $mqtt)
`function (topic, message, packet) {}`

当客户端收到Publish报文时触发
* `$topic`  收到的主题
* `$content` 收到的消息内容
* `$mqtt` mqtt客户端实例

-------------------------------------------------------

### callback onError(\Exception $exception)
当连接发生某种错误时触发.

-------------------------------------------------------

### callback onClose()
当连接关闭时触发，无论是客户端主动关闭还是服务端关闭都会触发onClose.

-------------------------------------------------------


### 参考项目
https://github.com/walkor/mqtt
