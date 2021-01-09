<?php

declare(strict_types=1);

namespace onlymcglobal\player\social;

use onlymcglobal\OnlyMCGlobalTrait;
use onlymcglobal\player\Player;
use pocketmine\plugin\PluginException;

class SocialFactory {

    use OnlyMCGlobalTrait;

    /** @var array<string, Party> */
    private array $partyStorage = [];

    public function init(): void {

    }

    /**
     * @param string $ownerName
     * @return Party
     */
    public function createParty(string $ownerName): Party {
        return $this->partyStorage[strtolower($ownerName)] = new Party($ownerName);
    }

    /**
     * @param Player $player
     * @return Party
     */
    public function getPartyNonNull(Player $player): Party {
        if (($party = $this->getParty($player)) !== null) {
            return $party;
        }

        throw new PluginException('Player without party');
    }

    /**
     * @param Player $player
     * @return Party|null
     */
    public function getParty(Player $player): ?Party {
        return $this->partyStorage[strtolower($player->getName())] ?? null;
    }
}