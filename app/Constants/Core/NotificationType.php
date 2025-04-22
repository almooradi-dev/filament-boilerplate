<?php

namespace App\Constants\Core;

class NotificationType
{
    public const GENERAL = 1;
    public const MESSAGE = 2;
    public const ANNOUNCEMENT = 3;
    public const ALERT = 4;
    public const REMINDER = 5;
    public const SYSTEM_UPDATE = 6;
    public const PROMOTION = 7;
    public const TASK = 8;
    public const WARNING = 9;
    public const EVENT = 10;
    public const FEEDBACK_REQUEST = 11;

    public const DEFAULT = self::GENERAL;

    public const KEYS = [
        self::GENERAL => 'general',
        self::MESSAGE => 'message',
        self::ANNOUNCEMENT => 'announcement',
        self::ALERT => 'alert',
        self::REMINDER => 'reminder',
        self::SYSTEM_UPDATE => 'system_update',
        self::PROMOTION => 'promotion',
        self::TASK => 'task',
        self::WARNING => 'warning',
        self::EVENT => 'event',
        self::FEEDBACK_REQUEST => 'feedback_request',
    ];
}
