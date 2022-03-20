<?php

namespace ree_jp\lobby;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use ree_jp\lobby\item\LobbyItem;

class EventListener implements Listener
{
    public function onJoin(PlayerJoinEvent $ev): void
    {
        LobbyPlugin::$store->clearALl($ev->getPlayer()->getXuid());

        $ev->getPlayer()->getInventory()->setItem(0, ItemFactory::getInstance()->get(ItemIds::COMPASS)->setCustomName("サーバー選択"));
        $ev->getPlayer()->getInventory()->setItem(8, ItemFactory::getInstance()->get(ItemIds::NETHER_STAR)->setCustomName("設定"));
        $ev->getPlayer()->getHungerManager()->setEnabled(false);
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
        $item = $ev->getItem();
        if ($item instanceof LobbyItem) {
            $item->onActive($ev->getPlayer());
        }
        $ev->cancel();
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