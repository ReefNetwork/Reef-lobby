<?php

namespace ree_jp\lobby\item;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use ree_jp\lobby\LobbyPlugin;

class Setting extends LobbyItem
{
    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemIds::NETHER_STAR, 0), "設定");
    }

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult
    {
        return $this->onActive($player);
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult
    {
        return $this->onActive($player);
    }

    static function onActive(Player $p): ItemUseResult
    {
        if (!LobbyPlugin::$store->hasValue($p->getXuid(), "form")) {
            $p->getServer()->dispatchCommand($p, "exe-p setting");
            LobbyPlugin::$store->setValue($p->getXuid(), "form", 10);
        }
        return ItemUseResult::SUCCESS();
    }
}