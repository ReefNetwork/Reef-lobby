<?php

namespace ree_jp\lobby;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class LobbyPlugin extends PluginBase
{
    static KeyValueStore $store;

    public function onEnable(): void
    {
        self::$store = new KeyValueStore($this->getScheduler());

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

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

            if ($p->getWorld()->getBlock($p->getPosition())->getTypeId() === VanillaBlocks::NETHER_PORTAL()->getTypeId()) {
                $block = $p->getWorld()->getBlock($p->getPosition()->subtract(0, 2, 0));
                switch ($block->getTypeId()) {
                    case VanillaBlocks::WOOL()->setColor(DyeColor::PINK()):
                        $this->transfer($p, "さくらサーバー");
                        break;
                    case VanillaBlocks::WOOL()->setColor(DyeColor::ORANGE()):
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
