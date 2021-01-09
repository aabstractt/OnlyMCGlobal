<?php

declare(strict_types=1);

namespace onlymcglobal\player;

use onlymcglobal\player\rank\Rank;
use onlymcglobal\player\rank\RankFactory;
use pocketmine\Server;

class PlayerStorage {

    /** @var string */
    private string $name;

    /**
     * PlayerStorage constructor.
     * @param string $name
     */
    public function __construct(string $name) {
        $this->name = $name;
    }

    /**
     * @param Rank $rank
     */
    public function setRank(Rank $rank): void {
        RankFactory::getInstance()->setPlayerRank($this->name, $rank);

        //Utils::calculatePlayerPermissions($this->name);
    }

    /**
     * @return Rank|null
     */
    public function getRank(): ?Rank {
        return RankFactory::getInstance()->getPlayerRank($this->name);
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PlayerStorage|null
     */
    public static function loadStorage(string $name): ?PlayerStorage {
        $storage = new PlayerStorage($name);

        if ($storage->getRank() !== null) {
            return $storage;
        }

        $storage = null;

        $player = Server::getInstance()->getPlayerByPrefix($name);

        if ($player !== null) $storage = new PlayerStorage($player->getName());

        return $storage;
    }
}