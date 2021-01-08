<?php

declare(strict_types=1);

namespace onlymcglobal\player;

use libBungeeCore\BungeeCore;
use libBungeeCore\BungeeCoreThread;
use libBungeeCore\packet\PlayerTransferPacket;
use libBungeeCore\packet\ScriptSharePacket;

class Player extends \pocketmine\player\Player {

    /**
     * @param string $description
     */
    public function connectTo(string $description): void {
        BungeeCore::getInstance()->sendPacket(PlayerTransferPacket::create($this->getName(), $description));
    }

    /**
     * Force connect to a server fallback
     */
    public function connectNowFallback(): void {
        BungeeCore::getInstance()->sendPacket(ScriptSharePacket::create(BungeeCoreThread::SEND_TO_FALLBACK, [BungeeCore::getServerDescription()]));
    }

    /**
     * Force send to a lobby
     */
    public function disconnectNow(): void {
        BungeeCore::getInstance()->sendPacket(ScriptSharePacket::create(BungeeCoreThread::SEND_TO_LOBBY, [BungeeCore::getServerDescription()]));
    }
}