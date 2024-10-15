<?php

$long_keyboard = [
    'inline_keyboard' => [
        [
            ['text' => '🇺🇿 O\'zbekcha', 'callback_data' => 'uz'],
            ['text' => '🇬🇧 English', 'callback_data' => 'en'],
            ['text' => '🇷🇺 Русский', 'callback_data' => 'ru'],
        ],
    ],
];

$uz_markup_keyboard = [
    'keyboard' => [
        ['Biz haqimizda'],
    ],
    'resize_keyboard' => true
];

$en_markup_keyboard = [
    'keyboard' => [
        ['About us'],
    ],
    'resize_keyboard' => true
];

$ru_markup_keyboard = [
    'keyboard' => [
        ['О нас'],
    ],
    'resize_keyboard' => true
];