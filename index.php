<?php
require 'vendor/autoload.php';

use Telegram\Bot\Api;

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

                if (strtolower($text) === '/start') {
                    sendMSG("Assalomu alaykum\nHello\nПривет", $chat_id, null);
                    sendMSG("Tilni tanlang\nChoose language\nВыберите язык", $chat_id, $long_keyboard);
                    $choose[$chat_id] = 'fName';
                } else switch ($choose[$chat_id]) {
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
                        sendMSG($content['uz']['fName'], $chatId, $uz_markup_keyboard);
                        break;
                    case 'en':
                        sendMSG($content['en']['fName'], $chatId, $en_markup_keyboard);
                        break;
                    case 'ru':
                        sendMSG($content['ru']['fName'], $chatId, $ru_markup_keyboard);
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