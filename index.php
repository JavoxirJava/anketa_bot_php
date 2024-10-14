<?php
require 'vendor/autoload.php';

use Telegram\Bot\Api;

$telegram = new Api('5169354010:AAFlFQCD4lk29l9FXfKYb7nTzZnbsfOohy0');

$lastUpdateId = 0;
$choose = array();
$fName = array();
$lName = array();

while (true) {
    $updates = $telegram->getUpdates(['offset' => $lastUpdateId + 1]);

    if (count($updates) > 0) {
        foreach ($updates as $update) {
            if (isset($update['message'])) {
                $chat_id = $update['message']['chat']['id'];
                $text = $update['message']['text'];

                if (strtolower($text) === '/start') {
                    $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => 'Assalomu alaykum hush kelibsiz Ismingizni kiriting: '
                    ]);
                    $choose[$chat_id] = 'fName';
                } else switch ($choose[$chat_id]) {
                    case 'fName':
                        $fName[$chat_id] = $text;
                        $telegram->sendMessage([
                            'chat_id' => $chat_id,
                            'text' => 'Familiyangizni kiriting: '
                        ]);
                        $choose[$chat_id] = 'lName';
                        break;
                    case 'lName':
                        $lName[$chat_id] = $text;
                        $telegram->sendMessage([
                            'chat_id' => $chat_id,
                            'text' => 'Telefon raqamingizni kiriting: '
                        ]);
                        $choose[$chat_id] = 'phone';
                        break;
                    case 'phone':
                        $telegram->sendMessage([
                            'chat_id' => $chat_id,
                            'text' => "Malumotlaringiz saqlandi! \nsizning malumotlaringiz\n\nIsmingiz: " .
                                $fName[$chat_id] . "\nFamiliyangiz: " . $lName[$chat_id] . "\nTelefon raqamingiz: " . $text
                        ]);
                        $choose[$chat_id] = 'address';
                        break;
                }
                $lastUpdateId = $update['update_id'];
            }
        }
    } else usleep(500000);
}
