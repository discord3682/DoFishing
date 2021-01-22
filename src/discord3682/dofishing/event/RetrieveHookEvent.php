<?php

namespace discord3682\dofishing\event;

use discord3682\dofishing\entity\FishingHook;
use pocketmine\event\Event;
use pocketmine\event\Cancellable;
use pocketmine\Player;

class RetrieveHookEvent extends Event implements Cancellable
{

  private $player;
  private $fishinghook;

  public function __construct (Player $player, FishingHook $fishinghook)
  {
    $this->player = $player;
    $this->fishinghook = $fishinghook;
  }

  public function getPlayer () : Player
  {
    return $this->player;
  }

  public function getFishingHook () : FishingHook
  {
    return $this->fishinghook;
  }
}
