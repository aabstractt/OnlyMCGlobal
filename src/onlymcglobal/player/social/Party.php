<?php

declare(strict_types=1);

namespace onlymcglobal\player\social;

use libBungeeCore\BungeeCore;
use libBungeeCore\BungeeServerInfo;
use onlymcglobal\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class Party {

    /** @var string */
    private string $ownerName;
    /** @var array<int, string> */
    private array $members;

    /**
     * Party constructor.
     * @param string $ownerName
     */
    public function __construct(string $ownerName) {
        $this->ownerName = $ownerName;
    }

    /**
     * @return string
     */
    public function getOwnerName(): string {
        return $this->ownerName;
    }

    /**
     * @return string[]
     */
    public function getMembers(): array {
        return $this->members;
    }

    /**
     * @return Player[]
     */
    public function getPlayers(): array {
        /** @var array<string, Player> $players */
        $players = [];

        foreach ($this->members as $member) {
            /** @var Player|null $player */
            $player = Server::getInstance()->getPlayerExact($member);

            if ($player == null) continue;

            $players[strtolower($player->getName())] = $player;
        }

        return $players;
    }

    /**
     * @param string $member
     */
    public function addMember(string $member): void {
        $this->members[] = $member;
    }

    /**
     * @param Player $player
     */
    public function addPlayerMember(Player $player): void {
        $this->addMember($player->getName());
    }

    /**
     * @param BungeeServerInfo $serverInfo
     * @param bool $force
     */
    public function connectNow(BungeeServerInfo $serverInfo, bool $force = false): void {
        if ($serverInfo->getOnlinePlayers() > $serverInfo->getMaxPlayers() || ($serverInfo->getOnlinePlayers() - count($this->getMembers())) > $serverInfo->getMaxPlayers()) {
            $this->broadcastMessage('&cServer is full');

            if ($force) {
                $this->connectNow(BungeeCore::getInstance()->getBetterServer($serverInfo->getServerGroup(), $serverInfo->isLobbyServer(), $serverInfo->isDefaultServer()));
            }

            return;
        }

        foreach ($this->getPlayers() as $player) {
            $player->connectNow($serverInfo);
        }
    }

    /**
     * @param string $message
     */
    public function broadcastMessage(string $message): void {
        foreach ($this->getPlayers() as $player) {
            $player->sendMessage(TextFormat::colorize($message));
        }
    }
}