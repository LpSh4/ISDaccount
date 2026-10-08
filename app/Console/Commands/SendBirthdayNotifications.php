<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use SergiX44\Nutgram\Nutgram;
use Carbon\Carbon;

class SendBirthdayNotifications extends Command
{
    protected $signature = 'bot:send-birthdays';
    protected $description = 'Отправка уведомлений о днях рождения и статистике подписчикам Telegram бота';
    public function handle(Nutgram $bot): void
    {
        $today = Carbon::today();$tomorrow = Carbon::tomorrow();

        $todayUsers = DB::table('users')
            ->whereNotNull('date_of_birth')
            ->whereMonth('date_of_birth', $today->month)
            ->whereDay('date_of_birth', $today->day)
            ->get();

        $tomorrowUsers = DB::table('users')
            ->whereNotNull('date_of_birth')
            ->whereMonth('date_of_birth', $tomorrow->month)
            ->whereDay('date_of_birth', $tomorrow->day)
            ->get();
        $noFeedingDays =$this->getCounterDays('no_feeding');
        $noIncidentsDays =$this->getCounterDays('no_incidents');
        if ($todayUsers->isEmpty() && $tomorrowUsers->isEmpty()) {$this->info('Ни сегодня, ни завтра именинников нет.');
            return;
        }

        $messageParts = [];
        if ($todayUsers->isNotEmpty()) {$todayNames = $todayUsers->map(function ($user) {
            $fullName = trim(e("{$user->surname} {$user->name} {$user->lastname}"));
            return "• " . ($fullName ?: e($user->name));
        })->implode("\n");

            $messageParts[] = "<b>Сегодня день рождения празднуют-></b>\n\n" . $todayNames . "\n\nПоздравляем! 🤟👺😈";
        }

        if ($tomorrowUsers->isNotEmpty()) {$tomorrowNames = $tomorrowUsers->map(function ($user) {
            $fullName = trim(e("{$user->surname} {$user->name} {$user->lastname}"));
            return "• " . ($fullName ?: e($user->name));
        })->implode("\n");

            $messageParts[] = "<b>⏰ Завтра день рождения празднуют-></b>\n\n" . $tomorrowNames . "\n\nНе забудьте подготовить поздравления!";
        }
        $statsBlock = "<b>📊 Текущая статистика:</b>\n"
            . "-Дней без кормежки-> <b>{$noFeedingDays}</b>\n"
            . "-Дней без происшествий-> <b>{$noIncidentsDays}</b>";

        $messageParts[] =$statsBlock;

        $fullMessage = implode("\n\n-------------------------\n\n", $messageParts);

        $subscribers = DB::table('telegram_subscribers')->pluck('chat_id');

        if ($subscribers->isEmpty()) {$this->warn('Нет подписчиков в базе для отправки.');
            return;
        }

        $sentCount = 0;
        foreach ($subscribers as$chatId) {
            try {
                $bot->sendMessage(
                    text: $fullMessage,
                    chat_id: $chatId,
                    parse_mode: 'HTML'
                );

                $sentCount++;
            } catch (\Throwable $e) {$this->error("Ошибка для ID {$chatId}: " . $e->getMessage());
                logger()->error("Не удалось отправить сообщение пользователю {$chatId}: " . $e->getMessage());
            }
        }

        $this->info("Уведомление отправлено {$sentCount} подписчикам.");
    }
    private function getCounterDays(string $key): int
    {
        $resetAt = DB::table('counters')->where('key',$key)->value('reset_at');

        if ($resetAt) {
            return (int) Carbon::parse($resetAt)->diffInDays(Carbon::now());
        }
        return 0;
    }
}
