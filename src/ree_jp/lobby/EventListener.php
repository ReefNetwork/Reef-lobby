<?php

namespace ree_jp\lobby;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class EventListener implements Listener
{
    public function onJoin(PlayerJoinEvent $ev): void
    {
        $ev->getPlayer()->getInventory()->setItem(0, ItemFactory::getInstance()->get(ItemIds::COMPASS));
        $ev->getPlayer()->getInventory()->setItem(8, ItemFactory::getInstance()->get(ItemIds::NETHER_STAR));
    }

    public function onDamage(EntityDamageEvent $ev): void
    {
        if ($ev->getEntity() instanceof Player) {
            switch ($ev->getCause()) {
                case EntityDamageEvent::CAUSE_FALL:
                    $ev->setBaseDamage(0);
                    break;

                case EntityDamageEvent::CAUSE_VOID:
                    $ev->getEntity()->teleport($ev->getEntity()->getWorld()->getSpawnLocation());
                    break;

                default:
                    $ev->cancel();
            }
        }
    }

    public function onTransaction(InventoryTransactionEvent $ev): void
    {
        if ($ev->getTransaction()->getSource()->isAdventure()) {
            $ev->cancel();
        }
    }
}