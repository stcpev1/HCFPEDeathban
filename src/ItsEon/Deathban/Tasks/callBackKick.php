<?php

namespace ItsEon\Deathban\Tasks;

use pocketmine\scheduler\PluginTask;

class callBackKick extends PluginTask
{
	private $main, $player;
	public function __construct(ItsEon\Deathban\main $main, \pocketmine\Player $player)
	{
		parent::__construct($main);
		$this->main = $main;
		$this->player = $player;
	}
	public function onRun(int $tick)
	{
		$this->player->kick('§8[§aHCF§8]§cDeathbanned for ' . $this->main->getBanTime($this->player), false);
	}
}
