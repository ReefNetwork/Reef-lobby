<?php

namespace ree_jp\lobby;

use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class KeyValueStore
{
    private array $values = array();

    public function __construct(private TaskScheduler $scheduler)
    {
    }

    public function setValue(string $xuid, string $value, int $tick = null, $data = null): void
    {
        $key = $xuid . ':' . $value;
        if ($tick === 0) {
            if ($this->hasValue($xuid, $value)) {
                unset($this->values[$key]);
            }
        } else {
            $this->values[$key] = $data;
            if (is_int($tick)) {
                $this->scheduler->scheduleDelayedTask(
                    new ClosureTask(function () use ($key): void {
                        if (array_key_exists($key, $this->values)) {
                            unset($this->values[$key]);
                        }
                    }), $tick);
            }
        }
    }

    public function hasValue(string $xuid, string $value): bool
    {
        $key = $xuid . ":" . $value;
        return array_key_exists($key, $this->values);
    }

    public function getValue(string $xuid, string $value): ?string
    {
        $key = $xuid . ":" . $value;
        return $this->values[$key] ?? null;
    }

    public function clearALl(string $xuid)
    {
        foreach ($this->values as $key => $value) {
            if (str_starts_with($key, $xuid . ":")) {
                unset($this->values[$key]);
            }
        }
    }
}