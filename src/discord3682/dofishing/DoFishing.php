<?php

namespace discord3682\dofishing;

use discord3682\dofishing\entity\FishingHook;
use pocketmine\plugin\PluginBase;
use pocketmine\entity\Entity;

class DoFishing extends PluginBase
{

  const FISHING = '§l§b[낚시]§r§7 ';

  public function onEnable () : void
  {
    $this->getServer ()->getPluginManager ()->registerEvents (new EventListener (), $this);
  }

  public function onLoad () : void
  {
    Entity::registerEntity (FishingHook::class, true, [
      'minecraft:fishinghook'
    ]);
  }

  public static function msg ($player, string $msg) : void
  {
    $player->sendMessage (self::FISHING . $msg);
  }
}
