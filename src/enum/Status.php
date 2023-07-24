<?php
namespace yii\payment\enum;

use MyCLabs\Enum\Enum;

class Status extends Enum
{
    private const SUCCESS = 'SUCCESS';
    private const FAILED = 'FAILED';
    private const PENDING = 'PENDING';

    /**
     * @return self
     */
    public static function SUCCESS(): self
    {
        return new Status(self::SUCCESS);
    }

    /**
     * @return self
     */
    public static function FAILED(): self
    {
        return new Status(self::FAILED);
    }

    /**
     * @return self
     */
    public static function PENDING(): self
    {
        return new Status(self::PENDING);
    }
}
