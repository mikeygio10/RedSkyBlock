<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use RedCraftPE\RedSkyBlock\Commands\Island;

class Delete {

  private static $instance;

  public function __construct() {

    self::$instance = $this;
  }

  public function onDeleteCommand(CommandSender $sender): bool {

    //This is a place holder for a command which will be implemented in a future update.
    return false;
  }
}
