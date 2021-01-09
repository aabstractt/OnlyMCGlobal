<?php

declare(strict_types=1);

namespace onlymcglobal\player;

use libBungeeCore\BungeeCore;
use libBungeeCore\BungeeCoreThread;
use libBungeeCore\BungeeServerInfo;
use libBungeeCore\packet\PlayerTransferPacket;
use libBungeeCore\packet\ScriptSharePacket;

class Player extends \pocketmine\player\Player {

    /**
     * @param BungeeServerInfo $serverInfo
     */
    public function connectNow(BungeeServerInfo $serverInfo): void {
        if (!BungeeCore::getInstance()->isConnected()) {
            throw new PlayerException(PlayerException::BUNGEECORE_OFFLINE);
        }

        BungeeCore::getInstance()->sendPacket(PlayerTransferPacket::create($this->getName(), $serverInfo->getServerDescription()));
    }

    /**
     * Force connect to a server fallback
     */
    public function connectNowFallback(): void {
        if (!BungeeCore::getInstance()->isConnected()) {
            throw new PlayerException(PlayerException::BUNGEECORE_OFFLINE);
        }

        BungeeCore::getInstance()->sendPacket(ScriptSharePacket::create(BungeeCoreThread::SEND_TO_FALLBACK, [BungeeCore::getInstance()->getCurrentServer()->getServerDescription()]));
    }

    /**
     * Force send to a lobby
     */
    public function disconnectNow(): void {
        if (!BungeeCore::getInstance()->isConnected()) {
            throw new PlayerException(PlayerException::BUNGEECORE_OFFLINE);
        }

        BungeeCore::getInstance()->sendPacket(ScriptSharePacket::create(BungeeCoreThread::SEND_TO_LOBBY, [BungeeCore::getInstance()->getCurrentServer()->getServerDescription()]));
    }
}