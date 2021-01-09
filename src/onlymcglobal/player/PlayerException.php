<?php

declare(strict_types=1);

namespace onlymcglobal\player;

use libBungeeCore\packet\ClientConnectionPacket;
use pocketmine\plugin\PluginException;

class PlayerException extends PluginException {

    /** @var string */
    const BUNGEECORE_CLIENT_LOADING = 'Client is loading all servers...';
    /** @var string */
    const BUNGEECORE_OFFLINE = ClientConnectionPacket::SERVER_SHUTDOWN;
}