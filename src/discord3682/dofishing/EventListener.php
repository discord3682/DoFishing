<?php

namespace discord3682\dofishing;

use discord3682\dofishing\utils\Utils;
use pocketmine\level\sound\LaunchSound;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\Listener;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\Player;

class EventListener implements Listener
{

  public function onEntityTeleport (EntityTeleportEvent $ev) : void
  {
    $entity = $ev->getEntity ();

    if ($entity instanceof Player)
    {
      if (Utils::isFishing ($entity))
      {
        Utils::retrieveHook ($entity);
      }
    }
  }

  public function onPlayerInteract (PlayerInteractEvent $ev) : void
  {
    $player = $ev->getPlayer ();
    $item = $ev->getItem ();

    if ($item->getId () === Item::FISHING_ROD)
    {
      if (!Utils::isCooldown ($player))
      {
        if (!Utils::isFishing ($player))
        {
          $direction = $player->getDirectionVector ();
          $nbt = Entity::createBaseNBT ($player->add (0, $player->getEyeHeight ()), $player->getDirectionVector()->multiply (0.7));
          $entity = Entity::createEntity ('FishingHook', $player->level, $nbt);
          $entity->setShooter ($player);
          $entity->spawnToAll ();
          $player->level->addSound (new LaunchSound ($player), $player->getViewers ());

          Utils::setCooldown ($player);
          Utils::addFishing ($player, $entity);
        }else
        {
          $entity = Utils::getFishingHook ($player);

          if (($reward = $entity->getReward ()) instanceof Item)
          {
            $player->getInventory ()->addItem ($reward);
            $reward = $reward->hasCustomName () ? $reward->getCustomName () : $reward->getName ();

            DoFishing::msg ($player, '§b' . $reward . '§r§7 을(를) 낚으셨습니다.');
          }else
          {
            DoFishing::msg ($player, '낚시를 취소하셨습니다.');
          }

          Utils::retrieveHook ($player);
          Utils::setCooldown ($player);
        }
      }
    }
  }

  public function onPlayerQuit (PlayerQuitEvent $ev) : void
  {
    $player = $ev->getPlayer ();

    if (Utils::isFishing ($player))
    {
      Utils::retrieveHook ($player);
    }
  }

  public function onPlayerDeath (PlayerDeathEvent $ev) : void
  {
    $player = $ev->getPlayer ();

    if (Utils::isFishing ($player))
    {
      Utils::retrieveHook ($player);
    }
  }
}
