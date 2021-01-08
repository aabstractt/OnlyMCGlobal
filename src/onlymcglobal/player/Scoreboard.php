<?php

declare(strict_types=1);

namespace onlymcglobal\player;

use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\utils\TextFormat;

class Scoreboard {


    /** @var string */
    public const LIST = 'list';
    public const SIDEBAR = 'sidebar';

    /** @var int */
    public const ASCENDING = 0;
    public const DESCENDING = 1;

    /** @var string */
    public string $displayName;
    /** @var string */
    private string $objectiveName;
    /** @var string */
    private string $displaySlot;
    /** @var int */
    private int $sortOrder;

    /**
     * Scoreboard constructor.
     * @param string $title
     * @param string $displaySlot
     * @param int $sortOrder
     */
    public function __construct(string $title, string $displaySlot, int $sortOrder = self::DESCENDING) {
        $this->displayName = $title;

        $this->objectiveName = uniqid('', true);

        $this->displaySlot = $displaySlot;

        $this->sortOrder = $sortOrder;
    }

    /**
     * @param Player[] $players
     */
    public function addPlayer(array $players): void {
        $pk = new SetDisplayObjectivePacket();

        $pk->displaySlot = $this->displaySlot;

        $pk->objectiveName = $this->objectiveName;

        $pk->displayName = $this->displayName;

        $pk->criteriaName = 'dummy';

        $pk->sortOrder = $this->sortOrder;

        foreach ($players as $p ) {
            $p->getNetworkSession()->sendDataPacket($pk);
        }
    }

    /**
     * @param Player[] $players
     */
    public function removePlayer(array $players): void {
        $pk = new RemoveObjectivePacket();

        $pk->objectiveName = $this->objectiveName;

        foreach ($players as $p ) {
            $p->getNetworkSession()->sendDataPacket($pk);
        }
    }

    /**
     * @param Player[] $players
     * @param int $line
     * @param string $message
     */
    public function setLine(array $players, int $line, string $message = ''): void {
        $this->setLines($players, [$line => $message]);
    }

    /**
     * @param Player[] $players
     * @param array $lines
     */
    public function setLines(array $players, array $lines): void {
        foreach ($players as $player) {
            $player->getNetworkSession()->sendDataPacket($this->getPackets($lines, SetScorePacket::TYPE_REMOVE));

            $player->getNetworkSession()->sendDataPacket($this->getPackets($lines, SetScorePacket::TYPE_CHANGE));
        }
    }

    /**
     * @param array $lines
     * @param int $type
     * @return ClientboundPacket
     */
    public function getPackets(array $lines, int $type): ClientboundPacket {
        $pk = new SetScorePacket();

        $pk->type = $type;

        foreach ($lines as $line => $message) {
            $entry = new ScorePacketEntry();

            $entry->objectiveName = $this->objectiveName;

            $entry->score = $line;

            $entry->scoreboardId = $line;

            if ($type === SetScorePacket::TYPE_CHANGE) {
                if ($message === '') {
                    $message = str_repeat(' ', $line - 1);
                }

                $entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;

                $entry->customName = TextFormat::colorize($message) . ' ';
            }

            $pk->entries[] = $entry;
        }

        return $pk;
    }
}