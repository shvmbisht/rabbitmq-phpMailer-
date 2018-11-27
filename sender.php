<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
 
$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
 
$channel->queue_declare('email_queue1', false, false, false, false);
 
$data = json_encode($_POST);
 
$msg = new AMQPMessage($data, array('delivery_mode' => 2));
$channel->basic_publish($msg, '', 'email_queue1');

$channel->close();
$connection->close();

 
header('Location: form.php?sent=true');