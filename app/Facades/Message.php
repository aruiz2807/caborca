<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\Message send(int $userId, string $title, string $message)
 * @method static bool markAsRead(\App\Models\Message $message)
 *
 * @see \App\Services\MessageService
 */
class Message extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'message';
    }
}
