<?php
require 'vendor/autoload.php';

use Telegram\Bot\Api;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$telegram = new Api('7383627105:AAFrktVAWW7g6tIiNwxd8pi8xsGLMPR_8ZQ'); // @full_testbot

static $count = 1;

//Colors
$red = "\033[31m";
$green = "\033[32m";
$blue = "\033[34m";
$yellow = "\033[33m";
$reset = "\033[0m"; // Ranglarni tiklash

$lastUpdateId = 0;
$choose = array();
$fName = array();
$phone = array();
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
                if (!isset($choose[$chat_id])) $choose[$chat_id] = null;
                if (!isset($chooseLang[$chat_id])) $chooseLang[$chat_id] = 'uz';
                switch ($text) {
                    case '/start':
                        sendMSG($content['hello'], $chat_id, null);
                        sendMSG($content['lang'], $chat_id, $long_keyboard);
                        $choose[$chat_id] = 'fName';
                        break;
                    case $content[$chooseLang[$chat_id]]['about']:
                        sendMSG($content[$chooseLang[$chat_id]]['about us'], $chat_id, null);
                        console($chat_id, 'about');
                        break;
                    default:
                        console($chat_id, $text);
                        switch ($choose[$chat_id]) {
                            case 'fName':
                                $fName[$chat_id] = $text;
                                $choose[$chat_id] = 'phone';
                                sendMSG($content[$chooseLang[$chat_id]]['phone'], $chat_id, null);
                                break;
                            case 'phone':
                                $phone[$chat_id] = $text;
                                $choose[$chat_id] = 'appeal';
                                sendMSG($content[$chooseLang[$chat_id]]['appeal'], $chat_id, null);
                                break;
                            case 'appeal':
                                sendMSG($content[$chooseLang[$chat_id]]['end'], $chat_id, null);
                                sendMail($chat_id, $fName[$chat_id], $phone[$chat_id], $text);
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

function sendMail($chat_id, $fName, $phone, $appeal) {
    $mail = new PHPMailer(true);
    try {
        // Server sozlamalari
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  // SMTP serveringizni qo'shing
        $mail->SMTPAuth   = true;
        $mail->Username   = 'javoxir8177@gmail.com'; // SMTP email
        $mail->Password   = 'mvrnlioxqiplosfd';          // SMTP email parol: mvrn liox qipl osfd
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Qabul qiluvchini sozlash
        $mail->setFrom('javoxir8177@gmail.com', 'Appeal'); // Yuboruvchi email va ismi
        $mail->addAddress('javoxir8177@gmail.com', 'Car road sign'); // Qabul qiluvchi email va ismi //TODO anticor.uzavtoyulbelgi@mail.ru

        // Kontent
        $mail->isHTML();
        $mail->Subject = 'Telegram Bot - Appeal';
        $mail->Body    = "<h1>Telegram Bot - Appeal</h1>
            <h2><b>Full Name: </b>{$fName}</h2>
            <h2><b>Phone: </b><a href='tel:{$phone}'>{$phone}</a></h2>
            <h2><b>Appeal: </b>{$appeal}</h2>";

        // Emailni jo'natish
        $mail->send();
        console($chat_id, 'The information has been sent to email successfully');
    } catch (Exception $e) {
        echo "Emailni jo'natishda xatolik: {$mail->ErrorInfo}";
    }
}

function console($chat_id, $text) {
    global $green, $yellow , $blue, $reset, $choose, $count;
    echo $yellow . "$count -> " . $blue . "Id: $chat_id, {$green}Status $choose[$chat_id]: $text\n" . $reset;
    $count++;
}