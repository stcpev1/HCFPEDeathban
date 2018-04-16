<?php

namespace ItsEon\Deathban;

use pocketmine\scheduler\PluginTask;

class unBanTask extends PluginTask
{
	private $main;
	public function __construct(Main $main)
	{
		parent::__construct($main);
		$this->main = $main;
	}
	public function onRun(int $tick)
	{
		$this->main->doTick();
	}
}