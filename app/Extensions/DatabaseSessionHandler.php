<?php

declare(strict_types=1);

namespace App\Extensions;

/*
 * This handler extends the laravel database session handler, and removes the user agent from the session, since this
 * information is not needed.
 */

class DatabaseSessionHandler extends \Illuminate\Session\DatabaseSessionHandler
{
    protected function addRequestInformation(&$payload)
    {
        parent::addRequestInformation($payload);

        // Remove user-agent, since we don't want to save this
        unset($payload['user_agent']);

        return $this;
    }
}
