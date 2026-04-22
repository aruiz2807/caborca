<?php

namespace App\Enums;

enum MessageStatus: string
{
    case NEW = 'New';
    case READED = 'Readed';

    /**
     * Get the translation for the enum value.
     */
    public function label(): string
    {
        return match ($this) {
            self::NEW => __('app.new'),
            self::READED => __('app.readed'),
        };
    }
}
