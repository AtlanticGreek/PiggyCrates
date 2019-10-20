<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyCrates\commands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyCrates\PiggyCrates;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

/**
 * Class KeyCommand
 * @package DaPigGuy\PiggyCrates\commands
 */
class KeyCommand extends BaseCommand
{
    /** @var PiggyCrates */
    private $plugin;

    /**
     * @param PiggyCrates $plugin
     * @param string $name
     * @param string $description
     * @param string[] $aliases
     */
    public function __construct(PiggyCrates $plugin, string $name, string $description = "", array $aliases = [])
    {
        $this->plugin = $plugin;
        parent::__construct($name, $description, $aliases);
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player && !isset($args["player"])) {
            $sender->sendMessage("Usage: /key <type> <amount> <player>");
            return;
        }
        $target = isset($args["player"]) ? $this->plugin->getServer()->getPlayer($args["player"]) : $sender;
        if (!$target instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Invalid player.");
            return;
        }
        $amount = $args["amount"] ?? 1;
        if (!is_numeric($amount)) {
            $sender->sendMessage(TextFormat::RED . "Amount must be numeric.");
            return;
        }
        $crate = PiggyCrates::getCrate($args["type"]);
        if ($crate === null) {
            $sender->sendMessage(TextFormat::RED . "Invalid crate type.");
            return;
        }
        $crate->giveKey($target, $amount);
        $target->sendMessage(TextFormat::GREEN . "You've received the " . $crate->getName() . " key.");
        $sender->sendMessage(TextFormat::GREEN . "You've given " . $target->getName() . " the " . $crate->getName() . " key.");

    }

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("type"));
        $this->registerArgument(1, new IntegerArgument("amount", true));
        $this->registerArgument(2, new RawStringArgument("player", true));
    }
}