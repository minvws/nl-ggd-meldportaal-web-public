<?php

declare(strict_types=1);

namespace App\LogEvents;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

class BsnLookupLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '080401';
    public const EVENT_KEY = 'bsn_lookup';
}
