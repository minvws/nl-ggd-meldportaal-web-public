<?php

declare(strict_types=1);

namespace App\LogEvents;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

class ReportLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090631';
    public const EVENT_KEY = 'report';
}
