<?php

declare(strict_types=1);

namespace onlymcglobal\player\task;

use onlymcglobal\OnlyMCGlobal;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class ScoreboardUpdateTask extends Task {

    /**
     * Actions to execute when run
     */
    public function onRun(): void {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            OnlyMCGlobal::getScoreboard()->setLines([$player], [
                10 => ' Conexión: &a' . $player->getNetworkSession()->getPing(),
                4 => ' Conectados: &a' . count(Server::getInstance()->getOnlinePlayers())
            ]);
        }
    }
}