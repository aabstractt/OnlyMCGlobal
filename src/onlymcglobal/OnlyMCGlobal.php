<?php

declare(strict_types=1);

namespace onlymcglobal;

use libBungeeCore\BungeeCore;
use libBungeeCore\packet\ClientConnectionPacket;
use onlymcglobal\listener\PlayerListener;
use onlymcglobal\player\Player;
use onlymcglobal\player\PlayerException;
use onlymcglobal\player\Scoreboard;
use onlymcglobal\player\task\ScoreboardUpdateTask;
use onlymcglobal\translation\TranslationFactory;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class OnlyMCGlobal extends PluginBase {

    /** @var OnlyMCGlobal */
    private static OnlyMCGlobal $instance;
    /** @var Scoreboard */
    private static Scoreboard $scoreboard;

    /**
     * @return OnlyMCGlobal
     */
    public static function getInstance(): OnlyMCGlobal {
        return self::$instance;
    }

    /**
     * @return Scoreboard
     */
    public static function getScoreboard(): Scoreboard {
        return self::$scoreboard;
    }

    public function onEnable(): void {
        self::$instance = $this;

        BungeeCore::getInstance()->init();

        TranslationFactory::getInstance()->init();

        if (BungeeCore::getInstance()->getCurrentServer()->isDefaultServer()) {
            $this->getScheduler()->scheduleRepeatingTask(new ScoreboardUpdateTask(), 20);
        }

        $this->registerListeners(new PlayerListener());
    }

    public function onDisable(): void {
        if (!BungeeCore::getInstance()->isConnected()) return;

        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            try {
                /** @var Player $player */
                $player->connectNowFallback();

                $player->sendMessage("§9BungeeCore> §6Transferring to a fallback server due to restart.");
            } catch (PlayerException $e) {
                $player->kick($e->getMessage());
            }
        }

        BungeeCore::getInstance()->sendPacket(ClientConnectionPacket::create(ClientConnectionPacket::CONNECTION_CLOSED, ClientConnectionPacket::CLIENT_SHUTDOWN));
    }

    /**
     * @param Listener ...$listeners
     */
    public function registerListeners(Listener ...$listeners): void {
        foreach ($listeners as $listener) {
            $this->getServer()->getPluginManager()->registerEvents($listener, $this);
        }
    }

    /**
     * @param string $config
     * @return Config
     */
    public function getConfiguration(string $config): Config {
        return new Config($this->getDataFolder() . $config);
    }

    /**
     * @return string
     */
    public final static function getDefaultScoreboardFormat(): string {
        return TextFormat::colorize(self::$instance->getConfig()->getNested('default-rank-data.default-scoreboard-format'));
    }
}