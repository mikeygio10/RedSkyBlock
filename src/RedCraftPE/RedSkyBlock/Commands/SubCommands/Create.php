<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\item\Item;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Tasks\Generate;
use RedCraftPE\RedSkyBlock\Commands\Island;

class Create {

  private static $instance;

  public function __construct() {

    self::$instance = $this;
  }

  public function onCreateCommand(CommandSender $sender): bool {

    if ($sender->hasPermission("skyblock.create")) {

      $interval = SkyBlock::getInstance()->cfg->get("Interval");
      $itemsArray = SkyBlock::getInstance()->cfg->get("Starting Items", []);
      $levelName = SkyBlock::getInstance()->cfg->get("SkyBlockWorld");
      $skyblockArray = SkyBlock::getInstance()->skyblock->get("SkyBlock", []);
      $islands = SkyBlock::getInstance()->skyblock->get("Islands");
      $senderName = strtolower($sender->getName());
      $level = null;
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
      if (array_key_exists($senderName, $skyblockArray)) {

        $sender->sendMessage(TextFormat::RED . "You already have an island.");
        return true;
      } else {

        $sender->teleport(new Position($islands * $interval + 2, 18, $islands * $interval + 4, $level));
        $sender->setImmobile(true);
        SkyBlock::getInstance()->getScheduler()->scheduleDelayedTask(new Generate($islands, $level, $interval, $sender), 10);

        foreach($itemsArray as $items) {

          if (count($itemsArray) > 0) {

            $itemArray = explode(" ", $items);
            if (count($itemArray) === 3) {

              $id = intval($itemArray[0]);
              $damage = intval($itemArray[1]);
              $count = intval($itemArray[2]);
              $sender->getInventory()->addItem(Item::get($id, $damage, $count));
            }
          }
        }

        SkyBlock::getInstance()->skyblock->setNested("Islands", $islands + 1);
        $skyblockArray[$senderName] = Array("Name" => $sender->getName() . "'s Island", "Members" => [$sender->getName()], "Banned" => [], "Locked" => false, "Area" => Array("start" => Array("X" => ($islands * $interval + 2) - 50, "Y" => 0, "Z" => ($islands * $interval + 4) - 50), "end" => Array("X" => ($islands * $interval + 2) + 50, "Y" => 256, "Z" => ($islands * $interval + 4) + 50)));
        SkyBlock::getInstance()->skyblock->set("SkyBlock", $skyblockArray);
        SkyBlock::getInstance()->skyblock->save();
        return true;
      }
    } else {

      $sender->sendMessage(TextFormat::RED . "You do not have the proper permissions to run this command.");
      return true;
    }
  }
}
