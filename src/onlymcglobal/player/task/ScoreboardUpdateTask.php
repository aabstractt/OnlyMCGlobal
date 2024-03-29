<?php

declare(strict_types=1);

namespace onlymcglobal\player\task;

use libBungeeCore\BungeeCore;
use onlymcglobal\OnlyMCGlobal;
use onlymcglobal\player\Player;
use onlymcglobal\translation\Translation;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class ScoreboardUpdateTask extends Task {

    /**
     * Actions to execute when run
     */
    public function onRun(): void {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            /** @var Player $player */
            $rankString = OnlyMCGlobal::getDefaultScoreboardFormat();

            OnlyMCGlobal::getScoreboard()->setLines([$player], Translation::getInstance()->translateArray('LOBBY_SCOREBOARD_UPDATE', [
                $rankString,
                $player->getNetworkSession()->getPing(),
                10,
                3,
                BungeeCore::getInstance()->getCurrentServer()->getServerId(),
                count(Server::getInstance()->getOnlinePlayers())
            ]));
        }
    }
}