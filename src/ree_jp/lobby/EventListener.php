<?php

namespace ree_jp\lobby;

use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemUseResult;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class EventListener implements Listener
{
    public function onLogin(PlayerLoginEvent $ev): void
    {
        $p = $ev->getPlayer();
        LobbyPlugin::$store->clearALl($p->getXuid());

        $p->getInventory()->setItem(0, VanillaItems::CLOCK()->setCustomName("サーバーを選択"));
        $p->getInventory()->setItem(1, VanillaItems::BOOK()->setCustomName("最新情報"));
        $p->getInventory()->setItem(2, VanillaItems::PAPER()->setCustomName("オンラインのプレイヤー"));
        $p->getInventory()->setItem(7, VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::PLAYER())->asItem()->setCustomName("フレンド"));
        $p->getInventory()->setItem(8, VanillaItems::NETHER_STAR()->setCustomName("設定"));
        $p->getHungerManager()->setEnabled(false);

        $p->teleport(new Vector3(255.5 + mt_rand(-1, 1), 68, 256.5 + mt_rand(-1, 1)));
    }

    public function onJoin(PlayerJoinEvent $ev): void
    {
        $ev->getPlayer()->getServer()->dispatchCommand($ev->getPlayer(), "exe-p sp-form welcome_info");
    }

    private function reflectionCustomItemName(Item $item): Item
    {
        return $item->setCustomName($item->getName());
    }

    public function onDamage(EntityDamageEvent $ev): void
    {
        if ($ev->getEntity() instanceof Player) {
            switch ($ev->getCause()) {
                /** @noinspection PhpMissingBreakStatementInspection */
                case EntityDamageEvent::CAUSE_VOID:
                    $ev->getEntity()->teleport($ev->getEntity()->getWorld()->getSpawnLocation());
                case EntityDamageEvent::CAUSE_FALL:
                    $ev->setBaseDamage(0);
                    break;

                default:
                    $ev->cancel();
            }
        }
    }

    public function onTouch(PlayerInteractEvent $ev): void
    {
        $this->activeItem($ev->getItem(), $ev->getPlayer());
        if ($ev->getPlayer()->isSurvival()) $ev->cancel();
    }

    public function onUse(PlayerItemUseEvent $ev): void
    {
        $this->activeItem($ev->getItem(), $ev->getPlayer());
    }

    private function activeItem(Item $item, Player $p): void
    {
        if (LobbyPlugin::$store->hasValue($p->getXuid(), "use-item")) return;
        LobbyPlugin::$store->setValue($p->getXuid(), "use-item", 10);

        switch ($item->getTypeId()) {
            case VanillaItems::COMPASS()->getTypeId():
                $p->getServer()->dispatchCommand($p, "exe-p server-select");
                break;
            case VanillaItems::BOOK()->getTypeId():
                $p->getServer()->dispatchCommand($p, "exe-p wp-view");
                break;
            case VanillaItems::PAPER()->getTypeId():
                $p->getServer()->dispatchCommand($p, "exe-p list");
                break;
            case VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::PLAYER())->asItem()->getTypeId():
                $p->sendMessage("実装してない");
                break;
            case VanillaItems::NETHER_STAR()->getTypeId():
                $p->getServer()->dispatchCommand($p, "exe-p setting");
                break;
        }
    }

    public function onBreak(BlockBreakEvent $ev): void
    {
        if ($ev->getPlayer()->isSurvival()) $ev->cancel();
    }

    public function onPlace(BlockPlaceEvent $ev): void
    {
        if ($ev->getPlayer()->isSurvival()) $ev->cancel();
    }

    public function onBurn(BlockBurnEvent $ev): void
    {
        $ev->cancel();
    }

    public function onTransaction(InventoryTransactionEvent $ev): void
    {
        if ($ev->getTransaction()->getSource()->isSurvival()) {
            $ev->cancel();
        }
    }
}
