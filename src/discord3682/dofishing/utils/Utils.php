<?php

namespace discord3682\dofishing\utils;

use discord3682\dofishing\event\RetrieveHookEvent;
use discord3682\dofishing\entity\FishingHook;
use pocketmine\level\Level;
use pocketmine\item\Item;
use pocketmine\Player;

class Utils
{

  protected static $fishing = [];

  public static function retrieveHook (Player $player) : bool
  {
    if (isset (self::$fishing [self::convert ($player)]))
    {
      $ev = new RetrieveHookEvent ($player, self::getFishingHook ($player));
      $ev->call ();

      if ($ev->isCancelled ()) return false;

      $eid = self::$fishing [self::convert ($player)];

      if ($player->level instanceof Level)
      {
        $player->level->getEntity ($eid)->kill ();
        $player->level->getEntity ($eid)->close ();
        unset (self::$fishing [self::convert ($player)]);
      }
    }

    return false;
  }

  public static function getFishingHook (Player $player) : ?FishingHook
  {
    return isset (self::$fishing [self::convert ($player)]) ? $player->level->getEntity (self::$fishing [self::convert ($player)]) : null;
  }

  public static function isFishing ($player) : bool
  {
    return isset (self::$fishing [self::convert ($player)]);
  }

  public static function addFishing ($player, FishingHook $hook) : void
  {
    self::$fishing [self::convert ($player)] = $hook->getId ();
  }

  public static function unsetFishing ($player) : void
  {
    unset (self::$fishing [self::convert ($player)]);
  }

  protected static $cooldowns = [];

  public static function isCooldown ($player) : bool
  {
    $player = self::convert ($player);

    if (!isset (self::$cooldowns [$player]))
    {
      return false;
    }

    return (time () - self::$cooldowns [$player]) <= 1;
  }

  public static function setCooldown ($player) : void
  {
    self::$cooldowns [self::convert ($player)] = time ();
  }

  public static function getFishes () : Item
  {
    $fishes = [349, 349, 349, 349, 349, 349, 460, 460, 460, 462, 462, 462, 461];

    $rand = mt_rand (0, count ($fishes) - 1);

    return Item::get ($fishes [$rand], 0, 1);
  }

  public static function convert ($player) : string
  {
    return strtolower ($player instanceof Player ? $player->getName () : $player);
  }
}
