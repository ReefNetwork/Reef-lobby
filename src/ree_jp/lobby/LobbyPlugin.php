<?php

namespace ree_jp\lobby;

use pocketmine\block\BlockLegacyIds;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use ree_jp\lobby\item\Friends;
use ree_jp\lobby\item\PlayerList;
use ree_jp\lobby\item\ServerSelector;
use ree_jp\lobby\item\Setting;
use ree_jp\lobby\item\WPViewer;

class LobbyPlugin extends PluginBase
{
    static KeyValueStore $store;

    public function onEnable(): void
    {
        self::$store = new KeyValueStore($this->getScheduler());

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        ItemFactory::getInstance()->register(new ServerSelector(), true);
        ItemFactory::getInstance()->register(new WPViewer(), true);
        ItemFactory::getInstance()->register(new PlayerList(), true);
        ItemFactory::getInstance()->register(new Friends(), true);
        ItemFactory::getInstance()->register(new Setting(), true);

        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            $this->onCheck();
        }), 5);
    }

    public function onDisable(): void
    {
        parent::onDisable();
    }

    private function onCheck(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $p) {
            if (!$this->checkDistance($p->getPosition())) {
                $p->teleport($p->getWorld()->getSpawnLocation());
            }

            if ($p->getWorld()->getBlock($p->getPosition())->getId() === BlockLegacyIds::PORTAL) {
                $block = $p->getWorld()->getBlock($p->getPosition()->subtract(0, 2, 0));
                switch ($block->getId()) {
                    case 1:
                        $this->transfer($p, "さくらサーバー");
                        break;
                    case 2:
                        $this->transfer($p, "もみじサーバー");
                        break;
                }
            }
        }
    }

    private function checkDistance(Vector3 $pos): bool
    {
        return (abs(256 - $pos->getFloorX()) < 256) && abs(256 - $pos->getFloorZ()) < 256;
    }

    private function transfer(Player $p, string $ip, int $port = 0): void
    {
        $pk = new TransferPacket();
        $pk->address = $ip;
        $pk->port = $port;
        $p->getNetworkSession()->sendDataPacket($pk);
    }
}
