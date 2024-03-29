<?php

declare(strict_types=1);

namespace onlymcglobal\listener;

use libBungeeCore\BungeeCore;
use onlymcglobal\OnlyMCGlobal;
use onlymcglobal\player\Player;
use onlymcglobal\player\PlayerException;
use onlymcglobal\player\rank\RankFactory;
use onlymcglobal\translation\Translation;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;

class PlayerListener implements Listener {

    /**
     * @param PlayerCreationEvent $ev
     *
     * @priority NORMAL
     */
    public function onPlayerCreationEvent(PlayerCreationEvent $ev): void {
        $ev->setPlayerClass(Player::class);
    }

    /**
     * @param PlayerJoinEvent $ev
     *
     * @priority HIGHEST
     */
    public function onPlayerJoinEvent(PlayerJoinEvent $ev): void {
        /** @var Player $player */
        $player = $ev->getPlayer();

        if (!BungeeCore::getInstance()->isConnected()) {
            $player->kick(PlayerException::BUNGEECORE_OFFLINE);

            return;
        }

        if (!BungeeCore::getInstance()->getCurrentServer()->isDefaultServer()) return;

        $rankString = OnlyMCGlobal::getDefaultScoreboardFormat();

        $rank = RankFactory::getInstance()->getPlayerRank($player->getName());

        if ($rank !== null && !$rank->isDefault()) $rankString = $rank->getFormat();

        RankFactory::getInstance()->calculatePlayerPermissions($player->getName());

        OnlyMCGlobal::getScoreboard()->setLines([$player], Translation::getInstance()->translateArray('LOBBY_SCOREBOARD', [
            $rankString,
            $player->getNetworkSession()->getPing(),
            10,
            3,
            BungeeCore::getInstance()->getCurrentServer()->getServerId(),
            count(Server::getInstance()->getOnlinePlayers())
        ]));
    }
}