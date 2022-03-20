<?php

namespace ree_jp\lobby\item;

use pocketmine\block\Block;
use pocketmine\item\Compass;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class ServerSelector extends Compass
{
    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemIds::COMPASS, 0), "サーバーを選択");
    }

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult
    {
        return $this->onActive($player);
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult
    {
        return $this->onActive($player);
    }

    private function onActive(Player $p): ItemUseResult
    {
        $p->getServer()->dispatchCommand($p, "server-select");
        return ItemUseResult::SUCCESS();
    }
}