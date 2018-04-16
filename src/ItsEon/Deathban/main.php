<?php
namespace ItsEon\Deathban;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\permission;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\utils\Config;
class main extends PluginBase implements Listener
{
	private $bans = [], $banTime = 60;
	/*
	 * $bans = [
	 *[
	 *	"bans" => [
	 *		"name" => "name",
	 *		"ip" => "IP",
	 *		"cid" => "CID",
	 *	],
	 *	"time" => "time"
	 *]
	 */
	public function onEnable()
	{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new unBanTask($this), 20);
		((!file_exists($this->getDataFolder()) . "config.yml") ? $this->saveResource("config.yml", false) : '');
		$config = new Config($this->getDataFolder() . "config.yml");
		$this->banTime = ((is_numeric($time = $config->get("time"))) ? $time : 60);
	}
	public function onDisable()
	{
	}
	public function onJoin(PlayerJoinEvent $ev)
	{
		if ($this->isBanned($ev->getPlayer())) {
			$this->getServer()->getScheduler()->scheduleDelayedTask(new callBackKick($this, $ev->getPlayer()), 10);
		}
	}
	public function onDeath(PlayerDeathEvent $ev)
	{
		$this->addBan($ev->getPlayer());
		$ev->getPlayer()->kick('§8[§aHCF§8]§cDeathBanned For 15 Minutes ' . $this->getBanTime($ev->getPlayer()), false);
	}
	private function isBanned(Player $p)
	{
		foreach ($this->bans as $ban) {
			if ($ban['bans']['name'] == $p->getDisplayName()) {
				return true;
			}
			if ($ban['bans']['ip'] == $p->getAddress()) {
				return true;
			}
			if ($ban['bans']['cid'] == $p->getClientId()) {
				return true;
			}
		}
		return false;
	}
	private function addBan(Player $p)
	{
		$ban = [
			"bans" => [
				"name" => $p->getDisplayName(),
				"ip" => $p->getAddress(),
				"cid" => $p->getClientId(),
			],
			"time" => $this->banTime,
		];
		array_push($this->bans, $ban);
	}
	public function getBanTime(Player $p)
	{
		foreach ($this->bans as $ban) {
			if ($ban['bans']['name'] == $p->getDisplayName() OR $ban['bans']['ip'] == $p->getAddress() OR $ban['bans']['cid'] == $p->getClientId()) {
				/*
				if ($ban['time'] >= 86400) {
					$banT = $ban['time'] / 86400;
					$msg =  round($banT,5) . " Days";
				} elseif ($ban['time'] >= 3600) {
					$banT = $ban['time'] / 3600;
					$msg = round($banT,5)  . " Hours";
				} elseif ($ban['time'] >= 60) {
					$banT = $ban['time'] / 60;
					$msg = round($banT,5) . " Minutes";
				} elseif ($ban['time'] <= 60) {
					$msg = $ban['time'] . " Second";
				} else {
					$msg = 'unknown';
				}
				*/
				$hours = floor($ban['time'] / 3600);
				$minutes = floor(($ban['time'] / 60) % 60);
				$seconds = $ban['time'] % 60;
				$msg =
					(($hours > 1) ? "$hours Hour" . ($hours > 1 ? 's' : '') : '') .
					(($minutes > 1) ? " $minutes Minute" . ($minutes > 1 ? 's' : '') : '') .
					(($seconds > 1) ? " $seconds Second" . ($seconds > 1 ? 's' : '') : '');
				return $msg;
			}
		}
		return 0;
	}
	public function doTick()
	{
		$this->update();
	}
	private function update()
	{
		foreach ($this->bans as $index => $ban) {
			if ($ban['time'] < 0) {
				unset($this->bans[$index]);
			} else {
				$this->bans[$index]['time'] = $ban['time'] - 1;
			}
		}
	}
}
