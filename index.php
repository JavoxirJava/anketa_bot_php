<?php
require 'vendor/autoload.php';

use Telegram\Bot\Api;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$telegram = new Api('5169354010:AAFlFQCD4lk29l9FXfKYb7nTzZnbsfOohy0');

$lastUpdateId = 0;
$choose = array();
$fName = array();
$chooseLang = array();
$messageDeleted = false;

include $long_keyboard = 'buttons.php';
include $uz_markup_keyboard = 'buttons.php';
include $en_markup_keyboard = 'buttons.php';
include $ru_markup_keyboard = 'buttons.php';
include $content = 'content.php';

while (true) {
    $updates = $telegram->getUpdates(['offset' => $lastUpdateId + 1]);

    if (count($updates) > 0) {
        foreach ($updates as $update) {
            if (isset($update['message'])) {
                $chat_id = $update['message']['chat']['id'];
                $text = $update['message']['text'];
                switch ($text) {
                    case '/start':
                        sendMSG($content['hello'], $chat_id, null);
                        sendMSG($content['lang'], $chat_id, $long_keyboard);
                        $choose[$chat_id] = 'fName';
                        break;
                    case $content[$chooseLang[$chat_id]]['about']:
                            sendMSG($content[$chooseLang[$chat_id]]['about us'], $chat_id, null);
                        break;
                    default:
                        switch ($choose[$chat_id]) {
                            case 'fName':
                                $fName[$chat_id] = $text;
                                $choose[$chat_id] = 'phone';
                                sendMSG($content[$chooseLang[$chat_id]]['phone'], $chat_id, null);
                                break;
                            case 'phone':
                                $choose[$chat_id] = 'appeal';
                                sendMSG($content[$chooseLang[$chat_id]]['appeal'], $chat_id, null);
                                break;
                            case 'appeal':
                                sendMSG($content[$chooseLang[$chat_id]]['end'], $chat_id, null);
                                break;
                        }
                }
            } else if (isset($update['callback_query'])) {
                $callbackQuery = $update['callback_query'];
                $data = $callbackQuery['data'];
                $chatId = $callbackQuery['message']['chat']['id'];
                $chooseLang[$chatId] = $data;
                $choose[$chatId] = 'fName';
                $messageDeleted = true;
//                if ($messageDeleted) deleteMSG($callbackQuery['message']['message_id'], $chatId); TODO - Fix this
                switch ($data) {
                    case 'uz':
                        sendMSG($content[$data]['fName'], $chatId, $uz_markup_keyboard);
                        break;
                    case 'en':
                        sendMSG($content[$data]['fName'], $chatId, $en_markup_keyboard);
                        break;
                    case 'ru':
                        sendMSG($content[$data]['fName'], $chatId, $ru_markup_keyboard);
                        break;
                }
            }
            $lastUpdateId = $update['update_id'];
        }
    } else usleep(500000);
}

function sendMSG($text, $chat_id, $keyboard) {
    global $telegram;
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => $text,
        'reply_markup' => $keyboard ? json_encode($keyboard) : null
    ]);
}
function deleteMSG($chat_id, $message_id) {
    global $telegram;
    $telegram->deleteMessage([
        'chat_id' => $chat_id,
        'message_id' => $message_id
    ]);
}

function sendMail($text) {
    $mail = new PHPMailer(true);
    try {
        // Server sozlamalari
        $mail->isSMTP();
        $mail->Host       = 'localhost';  // SMTP serveringizni qo'shing
        $mail->SMTPAuth   = true;
        $mail->Username   = 'javoxir8177@gmail.com'; // SMTP email
        $mail->Password   = 'your-password';          // SMTP email parol
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Qabul qiluvchini sozlash
        $mail->setFrom('your-email@example.com', 'Telegram Bot');
        $mail->addAddress('recipient@example.com', 'Recipient Name');

        // Kontent
        $mail->isHTML();
        $mail->Subject = 'Telegram Bot Ma\'lumotlari';
        $mail->Body    = 'Sizning botingizdan olingan ma\'lumot: ' . $text;

        // Emailni jo'natish
        $mail->send();
        echo 'Email muvaffaqiyatli jo\'natildi';
    } catch (Exception $e) {
        echo "Emailni jo'natishda xatolik: {$mail->ErrorInfo}";
    }
}