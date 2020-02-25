<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/phpmailer/src/Exception.php';
require 'phpmailer/phpmailer/src/PHPMailer.php';
require 'phpmailer/phpmailer/src/SMTP.php';
function send_email($email,$message,$chat_id,$name) {
// Instantiation and passing `true` enables exceptions
  
  $mail = new PHPMailer(true);

  try {
      //Server settings
      $mail->SMTPDebug = 2;                                       // Enable verbose debug output
      $mail->isSMTP();                                            // Set mailer to use SMTP
      $mail->Host       = 'smtp.yandex.ru';  // Specify main and backup SMTP servers
      $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
      $mail->Username   = 'rinozz1@yandex.ru';                     // SMTP username
      $mail->Password   = '80662391610myac';                               // SMTP password
      $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
      $mail->Port       = 587;                                    // TCP port to connect to

      // $email = iconv("utf-8","cp1252", $email);
      // $message = iconv("utf-8","cp1252", $message);
      // $name = iconv("utf-8","cp1252", $name);
      // $mail->setFrom('rinozz1@yandex.ru', iconv("utf-8","windows-1252", $name));
      // $mail->addAddress('no-reply@renatumagulov.ru', 'Renat');     // Add a recipient

      // // Content
      // $mail->isHTML(true);                                  // Set email format to HTML
      // $mail->Subject = 'Message from '.iconv("utf-8","cp1252", $email).', $chat_id = '.$chat_id;
      // $mail->Body    = iconv("utf-8","cp1252", $message);no-reply@renatumagulov.ru
      //Recipients
      $mail->setFrom('rinozz1@yandex.ru', $name);
      $mail->addAddress('rinozz1@yandex.ru', 'Renat');     // Add a recipient

      // Content
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject = 'Message from telegram bot';
      $message = '<p>'.$message.'</p>';
      $message .= "<h3>From: <".$email.'> , $chat_id = '.$chat_id.'</h3>';
      $mail->Body    = $message;

      $mail->send();
      echo 'Message has been sent';
  } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}
?>