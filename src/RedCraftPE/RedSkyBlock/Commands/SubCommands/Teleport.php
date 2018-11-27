<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Commands\Island;

class Teleport {

  private static $instance;

  public function __construct() {

    self::$instance = $this;
  }

  public function onTeleportCommand(CommandSender $sender, array $args): bool {

    if ($sender->hasPermission("skyblock.create")) {

      $levelName = SkyBlock::getInstance()->cfg->get("SkyBlockWorld");

      if ($levelName === "") {

        $sender->sendMessage(TextFormat::RED . "You must set a SkyBlock world in order for this plugin to function properly.");
        return true;
      } else {

        if (SkyBlock::getInstance()->getServer()->isLevelLoaded($levelName)) {

          $level = SkyBlock::getInstance()->getServer()->getLevelByName($levelName);
        } else {

          if (SkyBlock::getInstance()->getServer()->loadLevel($levelName)) {

            SkyBlock::getInstance()->getServer()->loadLevel($levelName);
            $level = SkyBlock::getInstance()->getServer()->getLevelByName($levelName);
          } else {

            $sender->sendMessage(TextFormat::RED . "The world currently set as the SkyBlock world does not exist.");
            return true;
          }
        }
      }

      $level = SkyBlock::getInstance()->getServer()->getLevelByName($levelName);
      $skyblockArray = SkyBlock::getInstance()->skyblock->get("SkyBlock", []);
      $senderName = strtolower($sender->getName());

      if (count($args) < 2) {

        if (array_key_exists($senderName, $skyblockArray)) {

          $x = $skyblockArray[$senderName]["Area"]["start"]["X"];
          $z = $skyblockArray[$senderName]["Area"]["start"]["Z"];
          $sender->teleport(new Position($x + 50, 18, $z + 50, $level));
          $sender->sendMessage(TextFormat::GREEN . "You have been teleported to your island!");
          return true;
        } else {

          $sender->sendMessage(TextFormat::RED . "You do not have an island yet.");
          return true;
        }
      } else {

        if ($sender->hasPermission("skyblock.tp")) {

          $name = strtolower(implode(" ", array_slice($args, 1)));

          if (array_key_exists($name, $skyblockArray)) {

            if ($skyblockArray[$name]["Locked"] === false || in_array($sender->getName, $skyblockArray[$name]["Members"])) {

              $x = $skyblockArray[$name]["Area"]["start"]["X"];
              $z = $skyblockArray[$name]["Area"]["start"]["Z"];
              $sender->teleport(new Position($x + 50, 18, $z + 50, $level));
              $sender->sendMessage(TextFormat::GREEN . "Welcome to {$skyblockArray[$name]["Name"]}.");
              return true;
            } else {

              $sender->sendMessage(TextFormat::WHITE . implode(" ", array_slice($args, 1)) . "'s is locked.");
              return true;
            }
          } else {

            $sender->sendMessage(TextFormat::WHITE . implode(" ", array_slice($args, 1)) . TextFormat::RED . " does not have an island.");
            return true;
          }
        } else {

          $sender->sendMessage(TextFormat::RED . "You do not have the proper permissions to run this command.");
          return true;
        }
      }
    } else {

      $sender->sendMessage(TextFormat::RED . "You do not have the proper permissions to run this command.");
      return true;
    }
  }
}
