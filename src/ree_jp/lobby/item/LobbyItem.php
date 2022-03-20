<?php

namespace ree_jp\lobby\item;

use pocketmine\item\Item;
use pocketmine\item\ItemUseResult;
use pocketmine\player\Player;

abstract class LobbyItem extends Item
{
    abstract static function onActive(Player $p): ItemUseResult;
}