<?php

use SergiX44\Nutgram\Nutgram;
use Illuminate\Support\Facades\DB;

/** @var Nutgram $bot */

$bot->onCommand('start', function (Nutgram $bot) {
    $chatId = (string) $bot->chatId();

    // Записываем chat_id в нашу таблицу подписок
    DB::table('telegram_subscribers')->updateOrInsert(
        ['chat_id' => $chatId],
        ['created_at' => now(), 'updated_at' => now()]
    );

    $bot->sendMessage("лыптбдюл");
});
