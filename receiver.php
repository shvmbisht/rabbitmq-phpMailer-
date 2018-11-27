<?php
require_once __DIR__ . '/vendor/autoload.php';
require '/usr/share/php/libphp-phpmailer/class.phpmailer.php';
require '/usr/share/php/libphp-phpmailer/class.smtp.php';
use PhpAmqpLib\Connection\AMQPConnection;
 
$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
 
$channel->queue_declare('email_queue1', false, false, false, false);
 
echo ' * Waiting for messages. To exit press CTRL+C', "\n";
 
$callback = function($msg){
 
    echo " * Message received", "\n";
    $data = json_decode($msg->body, true);
 //error_log("DATA".json_encode($data));
    $from = $data['from'];
    $from_email = $data['from_email'];
    $to_email = $data['to_email'];
    $subject = $data['subject'];
    $message = $data['message'];
 

    $mail = new PHPMailer();

$mail->IsSMTP();

$mail->SMTPDebug = 1;

$mail->Host = "smtp.gmail.com"; // SMTP server example

$mail->SMTPAuth = true; // enable SMTP authentication

$mail->SMTPOptions = array(

'ssl' => array(

'verify_peer' => false,

'verify_peer_name' => false,

'allow_self_signed' => false

)

);

$mail->SMTPSecure = 'tls';

$mail->Port = 587; // set the SMTP port for the GMAIL server

$mail->Username = "shivam.bisht@mobiotics.com"; // SMTP account username example

$mail->Password = "4mobiotics.shiv"; // SMTP account password example


$mail->addAddress($data['to_email'], 'kamlesh rajpoot'); // Add a recipient

// $mail->addAddress(''); // Add a recipient

$mail->SetFrom($data['from_email'], $data['from']);

$mail->Subject = $data['subject'];

$mail->Body = $data['message'];

 
    if(!$mail->send()) {

        echo 'Message could not be sent.';
        
        echo 'Mailer Error: ' . $mail->ErrorInfo;
        
        exit;
        }
        echo 'Message has been sent';


$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    echo " * Message was sent", "\n";

};
 
$channel->basic_qos(null, 1, null);
$channel->basic_consume('email_queue1', '', false, false, false, false, $callback);
 
while(count($channel->callbacks)) {
    $channel->wait();
}


?>