<?php

declare(strict_types=1);

namespace onlymcglobal\player\rank\command;

use onlymcglobal\player\PlayerStorage;
use onlymcglobal\player\rank\Rank;
use onlymcglobal\player\rank\RankFactory;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class PermissionsCommand extends Command {

    /**
     * PermissionsCommand constructor.
     */
    public function __construct() {
        parent::__construct('permissions', 'Permissions command', '/permissions <args>');

        $this->setPermission('permissions.command');
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (empty($args)) {
            $sender->sendMessage(TextFormat::RED . 'Usage: ' . $this->getUsage());

            return;
        }

        $factory = RankFactory::getInstance();

        if ($args[0] == 'addrank') {
            if (!$sender->hasPermission($this->getPermission() . '.addrank')) {
                $sender->sendMessage(TextFormat::RED . 'You don\'t have permissions to use this command');

                return;
            }

            if (!isset($args[1], $args[2])) {
                $sender->sendMessage(TextFormat::RED . 'Usage: /' . $commandLabel . ' addrank <rank> <format>');

                return;
            }

            if ($factory->findRank($args[1])) {
                $sender->sendMessage(TextFormat::RED . 'Rank already exists');

                return;
            }
            $name = $args[1];
            $format = $args[2];

            unset($args[0], $args[1], $args[2]);

            $sender->sendMessage(TextFormat::GREEN . 'Added ' . $name . ' to the rank list successfully.');

            $sender->sendMessage(TextFormat::YELLOW . 'Use /' . $commandLabel . ' help to see the list of commands');

            $factory->createOrUpdate(new Rank(['name' => $name, 'format' => $format]));

            return;
        }

        if ($args[0] == 'formatrank') {
            if (!$sender->hasPermission($this->getPermission() . '.formatrank')) {
                $sender->sendMessage(TextFormat::RED . 'You don\'t have permissions to use this command');

                return;
            }

            if (!isset($args[1], $args[2])) {
                $sender->sendMessage(TextFormat::RED . 'Usage: /' . $commandLabel . ' formatrank <rank> <format>');

                return;
            }

            $rank = $factory->getRank($args[1]);

            if ($rank == null) {
                $sender->sendMessage(TextFormat::RED . 'This rank has not been found.');

                return;
            }

            unset($args[0], $args[1]);

            $format = implode(' ', array_shift($args));

            if(!stristr($format, '{username}')) {
                $sender->sendMessage(TextFormat::RED . 'You need to enter {username} for the username.');

                return;
            }

            if(!stristr($format, '{message}')) {
                $sender->sendMessage(TextFormat::RED . 'You need to enter {message} for the user\'s message name.');

                return;
            }

            $rank->setOriginalChatFormat($format);

            $sender->sendMessage(TextFormat::GREEN . 'You have configured the chat format of the ' . $rank->getOriginalChatFormat() . TextFormat::RESET . TextFormat::GREEN . ' rank correctly.');

            $sender->sendMessage(TextFormat::YELLOW . 'Use /' . $this->getName() . ' help to see the list of commands');

            $factory->createOrUpdate($rank);
            return;
        }

        if ($args[0] == 'nametagrank') {
            if (!$sender->hasPermission($this->getPermission() . '.nametagrank')) {
                $sender->sendMessage(TextFormat::RED . 'You don\'t have permissions to use this command');

                return;
            }

            if (!isset($args[1], $args[2])) {
                $sender->sendMessage(TextFormat::RED . 'Usage: /' . $commandLabel . ' nametagrank <rank> <format>');

                return;
            }

            $rank = $factory->getRank($args[1]);

            if ($rank == null) {
                $sender->sendMessage(TextFormat::RED . 'This rank has not been found.');

                return;
            }

            unset($args[0], $args[1]);

            $format = implode(' ', $args);

            if(!stristr($format, '{username}')) {
                $sender->sendMessage(TextFormat::RED . 'You need to enter {username} for the username.');

                return;
            }

            $rank->setOriginalNametag($format);

            $sender->sendMessage(TextFormat::GREEN . 'You have configured the nametag format of the ' . $rank->getOriginalNametagFormat() . TextFormat::RESET . TextFormat::GREEN . ' rank correctly.');

            $sender->sendMessage(TextFormat::YELLOW . 'Use /' . $this->getName() . ' help to see the list of commands');

            $factory->createOrUpdate($rank);
            return;
        }

        if ($args[0] == 'addrankpermission') {
            if (!$sender->hasPermission($this->getPermission() . '.addrankpermission')) {
                $sender->sendMessage(TextFormat::RED . 'You don\'t have permissions to use this command');

                return;
            }

            if (!isset($args[1], $args[2])) {
                $sender->sendMessage(TextFormat::RED . 'Usage: /' . $commandLabel . ' addrankpermission <rank> <permission>');

                return;
            }

            $rank = $factory->getRank($args[1]);

            if ($rank == null) {
                $sender->sendMessage(TextFormat::RED . 'This rank has not been found.');

                return;
            }

            if (in_array([$args[2]], $rank->getPermissionsWithoutInherited(), true)) {
                $sender->sendMessage(TextFormat::RED . 'This rank already has this permission.');

                return;
            }

            $permission = $args[2];

            $cmd = Server::getInstance()->getCommandMap()->getCommand($args[2]);

            if ($cmd !== null) $permission = $cmd->getPermission();

            if ($permission == null) return;

            $rank->addPermission($permission);

            $sender->sendMessage(TextFormat::GREEN . 'The permission has been successfully added to the ' . $rank->getName() . ' rank');

            $factory->createOrUpdate($rank);
            return;
        }

        if ($args[0] == 'adduserpermission') {
            if (!$sender->hasPermission($this->getPermission() . '.adduserpermission')) {
                $sender->sendMessage(TextFormat::RED . 'You don\'t have permissions to use this command');

                return;
            }

            if (!isset($args[1], $args[2])) {
                $sender->sendMessage(TextFormat::RED . 'Usage: /' . $commandLabel . ' adduserpermission <player> <permission>');

                return;
            }

            $playerStorage = PlayerStorage::loadStorage($args[1]);

            if ($playerStorage == null) {
                $sender->sendMessage(TextFormat::RED . 'Player not found');

                return;
            }

            $permission = $args[2];

            $cmd = Server::getInstance()->getCommandMap()->getCommand($args[2]);

            if ($cmd !== null) $permission = $cmd->getPermission();

            if ($permission == null) return;

            $factory->setPlayerPermission($playerStorage->getName(), $permission);

            $sender->sendMessage(TextFormat::GREEN . 'The permission has been successfully added to the ' . $playerStorage->getName() . ' user');

            return;
        }

        if ($args[0] == 'clearrankpermission') {
            if(!$sender->hasPermission($this->getPermission() . '.clearrankpermission')) {
                $sender->sendMessage(TextFormat::RED . 'You don\'t have permissions to use this command');

                return;
            }

            if(count($args) < 3) {
                $sender->sendMessage(TextFormat::RED . 'Usage: /' . $this->getName() . ' clearrankpermission <rank> <permission>');

                return;
            }

            $rank = $factory->getRank($args[1]);

            if($rank == null) {
                $sender->sendMessage(TextFormat::RED . 'This rank has not been found.');

                return;
            }

            $permission = $args[2];

            $cmd = Server::getInstance()->getCommandMap()->getCommand($args[2]);

            if ($cmd != null) $permission = $cmd->getPermission() ?? '';

            if(!in_array([$permission], $rank->getPermissionsWithoutInherited(), true)) {
                $sender->sendMessage(TextFormat::RED . 'This rank not has this permission.');

                return;
            }

            $rank->deletePermission($permission);

            $sender->sendMessage(TextFormat::GREEN . 'The permission has been successfully removed to the ' . $rank->getName() . ' rank');

            $factory->createOrUpdate($rank);

            return;
        }

        if ($args[0] == 'clearuserpermission') {
            if(!$sender->hasPermission($this->getPermission() . '.clearuserpermission')) {
                $sender->sendMessage(TextFormat::RED . 'You don\'t have permissions to use this command');

                return;
            }

            if(count($args) < 3) {
                $sender->sendMessage(TextFormat::RED . 'Usage: /' . $this->getName() . ' clearuserpermission <user> <permission>');

                return;
            }

            $playerStorage = PlayerStorage::loadStorage($args[1]);

            if ($playerStorage == null) {
                $sender->sendMessage(TextFormat::RED . 'Player not found');

                return;
            }

            $permission = $args[2];

            $cmd = Server::getInstance()->getCommandMap()->getCommand($args[2]);

            if ($cmd != null) $permission = $cmd->getPermission() ?? '';

            if(!in_array([$permission], $factory->getPlayerPermissions($playerStorage->getName()), true)) {
                $sender->sendMessage(TextFormat::RED . 'This user not has this permission.');

                return;
            }

            $factory->deletePlayerPermission($playerStorage->getName(), $permission);

            $sender->sendMessage(TextFormat::GREEN . 'The permission has been successfully removed to the ' . $playerStorage->getName() . ' user');

            return;
        }

        if ($args[0] == 'adduserrank') {
            if(!$sender->hasPermission($this->getPermission() . '.adduserrank')) {
                $sender->sendMessage(TextFormat::RED . 'You don\'t have permissions to use this command');

                return;
            }

            if(count($args) < 3) {
                $sender->sendMessage(TextFormat::RED . 'Usage: /' . $this->getName() . ' adduserrank <user> <rank>');

                return;
            }

            $playerStorage = PlayerStorage::loadStorage($args[1]);

            if ($playerStorage == null) {
                $sender->sendMessage(TextFormat::RED . 'Player not found');

                return;
            }

            $rank = $factory->getRank($args[2]);

            if($rank == null) {
                $sender->sendMessage(TextFormat::RED . 'This rank has not been found.');

                return;
            }

            $playerStorage->setRank($rank);

            $sender->sendMessage(TextFormat::BOLD . TextFormat::GREEN . 'You have given ' . $playerStorage->getName() . ' the ' . $rank->getName() . TextFormat::BOLD . TextFormat::GREEN . ' rank!');

            return;
        }
    }
}