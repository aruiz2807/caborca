<?php

namespace App\Services;

use App\Models\Message;
use App\Enums\MessageStatus;
use Illuminate\Support\Facades\Auth;

class MessageService
{
    /**
     * Send a message to a user.
     *
     * @param int $userId
     * @param string $title
     * @param string $message
     * @return Message
     */
    public function send(int $userId, string $title, string $message): Message
    {
        return Message::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'status' => MessageStatus::NEW,
        ]);
    }

    /**
     * Mark a message as read.
     *
     * @param Message $message
     * @return bool
     */
    public function markAsRead(Message $message): bool
    {
        return $message->update(['status' => MessageStatus::READED]);
    }
}
