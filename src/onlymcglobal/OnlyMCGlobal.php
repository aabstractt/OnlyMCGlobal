<?php

declare(strict_types=1);

namespace onlymcglobal;

use libBungeeCore\BungeeCore;
use pocketmine\plugin\PluginBase;

class OnlyMCGlobal extends PluginBase {

    /** @var OnlyMCGlobal */
    private static OnlyMCGlobal $instance;

    /**
     * @return OnlyMCGlobal
     */
    public static function getInstance(): OnlyMCGlobal {
        return self::$instance;
    }

    public function onEnable(): void {
        self::$instance = $this;

        BungeeCore::getInstance()->initThread();
    }
}