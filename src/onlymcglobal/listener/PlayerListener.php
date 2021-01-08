<?php

declare(strict_types=1);

namespace onlymcglobal\listener;

use libBungeeCore\BungeeCore;
use onlymcglobal\OnlyMCGlobal;
use onlymcglobal\player\Player;
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

        if (!BungeeCore::isDefaultServer()) return;

        $rankString = OnlyMCGlobal::getDefaultScoreboardFormat();

        //if ($rank !== null && !$rank->isDefault()) $rankString = $rank->getFormat();

        OnlyMCGlobal::getScoreboard()->setLines([$player], [
            12 => '',
            11 => ' Rango: ' . $rankString,
            10 => ' Conexión: &a' . $player->getNetworkSession()->getPing(),
            9 => '',
            8 => ' Baúles: &a10000',
            7 => ' Polvo Misterioso: &a100000',
            6 => '',
            5 => ' Lobby: &e#' . BungeeCore::getServerId(),
            4 => ' Conectados: &a' . count(Server::getInstance()->getOnlinePlayers()),
            3 => '',
            2 => '&e     mc.onlymc.us'
        ]);
    }
}