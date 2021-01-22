<?php

namespace discord3682\dofishing\entity;

use discord3682\dofishing\utils\Utils;
use discord3682\dofishing\DoFishing;
use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\item\Item;
use pocketmine\Player;

class FishingHook extends Entity
{

  const NETWORK_ID = Entity::FISHING_HOOK;

  public $width = 0.2;
  public $length = 0.2;
  public $height = 0.42;

  protected $fishTick = 350;
  protected $underTick = 65;
  protected $isUnderWater = false;
  protected $shooter = null;
  protected $reward = null;

  protected function initEntity () : void
  {
    $this->fishTick = mt_rand (130, 500);
    $this->setNameTagAlwaysVisible (true);

    parent::initEntity ();
  }

  public function getName () : string
  {
    return 'Fishing Hook';
  }

  final public function onUpdate (int $currentTick) : bool
  {
    if ($this->shooter === null or $this->closed)
    {
      return false;
    }

    $shooter = $this->shooter;

    if ($shooter->distance ($this) > 17)
    {
      DoFishing::msg ($shooter, '낚시줄이 끊겼습니다.');
      Utils::retrieveHook ($shooter);
      return false;
    }

    if (!$this->isUnderwater () and !$this->isUnderWater)
    {
      $this->underTick --;

      if ($this->underTick < 0)
      {
        DoFishing::msg ($shooter, '물이 있는 곳으로 던지십시오.');
        Utils::retrieveHook ($shooter);
      }
    }else
    {
      if (!$this->isUnderWater)
        $this->isUnderWater = true;

      $this->fishTick --;

      if ($this->fishTick <= 20 and $this->fishTick >= -20)
      {
        if ($this->reward === null)
        {
          $shooter->sendSubTitle ('§l§b!');
          $this->reward = Utils::getFishes ();
        }

        $this->setNameTag ('§l§b[' . Utils::convert ($shooter) . ']' . "\n" . '우클릭하여 획득');
      }else
      {
        if ($this->fishTick < -20)
        {
          if ($this->reward !== null)
          {
            $shooter->sendSubTitle ('§l§c. . .');
            $this->reward = null;
            $this->fishTick = mt_rand (130, 500);
          }
        }

        $this->setNameTag ('§l§c[' . Utils::convert ($shooter) . ']' . "\n" . '. . .');
      }
    }

    if ($this->level instanceof Level)
    {
      if ($this->isUnderwater ())
      {
        $this->gravity = -0.01;
      }else
      {
        $this->gravity = 0.03;
      }
    }

    return parent::onUpdate ($currentTick);
  }

  public function getShooter () : ?Player
  {
    return $this->shooter;
  }

  public function setShooter (?Player $player) : void
  {
    $this->shooter = $player;
  }

  public function getReward () : ?Item
  {
    return $this->reward;
  }

  public function setReward (Item $item) : void
  {
    $this->reward = $item;
  }
}
