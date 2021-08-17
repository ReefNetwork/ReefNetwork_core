<?php


/*
 * _____            __ _   _      _                      _
 *|  __ \          / _| \ | |    | |                    | |
 *| |__) |___  ___| |_|  \| | ___| |___      _____  _ __| | __
 *|  _  // _ \/ _ \  _| . ` |/ _ \ __\ \ /\ / / _ \| '__| |/ /
 *| | \ \  __/  __/ | | |\  |  __/ |_ \ V  V / (_) | |  |   <
 *|_|  \_\___|\___|_| |_| \_|\___|\__| \_/\_/ \___/|_|  |_|\_\
 *
 * ReefNetwork_core
 * @copyright 2019 Ree_jp
 */


namespace Ree\reef;


use bboyyu51\pmdiscord\connect\Webhook;
use bboyyu51\pmdiscord\Sender;
use bboyyu51\pmdiscord\structure\Content;
use bboyyu51\pmdiscord\structure\Embed;
use bboyyu51\pmdiscord\structure\Embeds;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;

class ReefAPI
{
	public static $news = "§aReef§eNetwork";

	const GOOD = "§a>> §r";
	const BAD = "§6>> §r";
	const ERROR = "§c>> §r";

	/**
	 * @param Player $p
	 * @return bool
	 */
	public static function isBan(Player $p): bool
	{
		$server = main::getMain();
		if ($server->banname->exists(mb_strtolower($p->getName()))) {
			return false;
		}
		if ($server->banip->exists($p->getAddress())) {
			return false;
		}
		return true;
	}

	/**
	 * @param string $n
	 * @return bool
	 */
	public static function NameBan($n): bool
	{
		$server = main::getMain();
		if ($server->banname->exists(mb_strtolower($n))) {
			return false;
		}
		$server->banname->set(mb_strtolower($n), "reason");
		return true;
	}

	/**
	 * @param $n
	 * @return bool
	 */
	public static function CancelNameBan($n): bool
	{
		$server = main::getMain();
		if (!$server->banname->exists(mb_strtolower($n))) {
			return false;
		}
		$server->banname->remove(mb_strtolower($n));
		return true;
	}

	/**
	 * @param $n
	 * @return bool
	 */
	public static function IpBan($n): bool
	{
		$server = main::getMain();
		$p = $server->getServer()->getPlayer($n);
		if ($server->banip->exists($p->getAddress())) {
			return false;
		}
		$server->banip->set($p->getAddress(), "reason");
		return true;
	}

	/**
	 * @param $ip
	 * @return bool
	 */
	public static function CancelIpBan($ip): bool
	{
		$server = main::getMain();
		if (!$server->banip->exists($ip)) {
			return false;
		}
		$server->banip->remove($ip);
		return true;
	}

	/**
	 * @param Player $p
	 * @param array $syogo
	 */
	public static function UpdateSyogo(Player $p, array $syogo = NULL): void
	{
		if ($syogo) {
			$pT = \Ree\seichi\main::getpT($p->getName());
			$level = $pT->getLevel();
			$syogo = \Ree\reef\main::getMain()->getSyogo()->get($p->getName());
			$p->setNameTag("[".$pT->getStar()." " . $level . "Lvl][" . $syogo[0] . "§r" . $syogo[1] . "§r" . $syogo[2] . "§r]" . $p->getName());
			$p->setDisplayName("[" .$pT->getStar(). $level . "Lvl][" . $syogo[0] . "§r" . $syogo[1] . "§r" . $syogo[2] . "§r]" . $p->getName());

			if ($p->isOp()) {
				$p->setNameTag("§dadomin§r[".$pT->getStar()." " . $level . "Lvl][" . $syogo[0] . "§r" . $syogo[1] . "§r" . $syogo[2] . "§r]" . $p->getName());
				$p->setDisplayName("§dadomin§r[".$pT->getStar()." " . $level . "Lvl][" . $syogo[0] . "§r" . $syogo[1] . "§r" . $syogo[2] . "§r]" . $p->getName());
			}
			if (self::isVip($p->getName())) {
				$p->setNameTag("§edonor§r[".$pT->getStar()." " . $level . "Lvl][" . $syogo[0] . "§r" . $syogo[1] . "§r" . $syogo[2] . "§r]" . $p->getName());
				$p->setDisplayName("§edonor§r[".$pT->getStar()." " . $level . "Lvl][" . $syogo[0] . "§r" . $syogo[1] . "§r" . $syogo[2] . "§r]" . $p->getName());
			}
			switch ($p->getName()) {
				case "Reejp":
					$p->setNameTag("§bowner§r[".$pT->getStar()." " . $level . "Lvl][" . $syogo[0] . "§r" . $syogo[1] . "§r" . $syogo[2] . "§r]" . $p->getName());
					$p->setDisplayName("§bowner§r[".$pT->getStar()." " . $level . "Lvl][" . $syogo[0] . "§r" . $syogo[1] . "§r" . $syogo[2] . "§r]" . $p->getName());
					break;
			}
		} else {
			$syogo = self::getSyogo($p);
			$p->setNameTag($syogo);
			$p->setDisplayName($syogo);
		}
	}

	/**
	 * @param Player $p
	 * @param string $syogo
	 */
	public static function addSyogo(Player $p, string $syogo): void
	{
		$list = main::getMain()->getSyogolist()->get($p->getName());
		$list[] = $syogo;
		main::getMain()->setSyogo($p, $list);
	}

	/**
	 * @param Player $p
	 * @param string $syogo
	 * @return bool
	 */
	public static function isHaveSyogo(Player $p, string $syogo): bool
	{
		$list = main::getMain()->getSyogolist()->get($p->getName());
		if (in_array($syogo, $list)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param Player $p
	 * @return string
	 */
	public static function getSyogo(Player $p)
	{
		$syogo = main::getMain()->getSyogo()->get($p->getName());
		$pT = \Ree\seichi\main::getpT($p->getName());
		$level = $pT->getLevel();

		$name = "[".$pT->getStar()." ". $level . "Lvl][" . $syogo[0] . "§r" . $syogo[1] . "§r" . $syogo[2] . "§r]" . $p->getName();
		if ($p->isOp()) {
			$name = "§dadmin§r[".$pT->getStar()." ".$level . "Lvl][" . $syogo[0] . "§r" . $syogo[1] . "§r" . $syogo[2] . "§r]" . $p->getName();
		}
		switch ($p->getName()) {
			case "Reejp":
				$name = "§bowner§r[".$pT->getStar()." " .$pT->getStar(). $level . "Lvl][" . $syogo[0] . "§r" . $syogo[1] . "§r" . $syogo[2] . "§r]" . $p->getName();
		}

		return $name;
	}

	public static function isVip(string $name): bool
	{
		if (main::getMain()->getVip()->exists($name)) {
			return true;
		} else {
			return false;
		}
	}

	public static function setVip(string $name, $bool = true): void
	{
		if ($bool) {
			if (!ReefAPI::isVip($name)) {
				main::getMain()->getVip()->set($name);
			}
		} elseif (ReefAPI::isVip($name)) {
			main::getMain()->getVip()->remove($name);
		}
		main::getMain()->getVip()->save();
	}

	/**
	 * @param Position $pos
	 * @param Player|NULL $p
	 * @return bool
	 */
	public static function isProtect(Position $pos, Player $p = NULL)
	{
		$level = $pos->getLevel()->getName();
		$n = "";
		$config = main::getMain()->getProtect();
		if ($p instanceof Player) {
			$n = $p->getName();
		}
		switch ($level) {
			case "lobby":
			case "public":
			case "Winter's Secret":
			case "Northpole":
				return false;
				break;

			case "leveling_1":
				return true;
				break;

			case "leveling_2":
				$array = $config->getAll();
				$keys = array_keys($array);
				$i = 0;

				foreach ($array as $pdata) {
					if (isset($pdata[$level])) {
						foreach ($pdata[$level] as $data) {
							if ($data["x1"] <= $pos->getFloorX() and $pos->getFloorX() <= $data["x2"]) {
								if ($data["z1"] <= $pos->getFloorZ() and $pos->getFloorZ() <= $data["z2"]) {
									if ($keys[$i] === $n) {
										return true;
									}elseif (in_array($n ,$data['sub']))
									{
										return true;
									}
								}
							}
						}
					}
					$i++;
				}
				return false;
				break;

			default:
				$array = main::getMain()->getProtect()->getAll();
				$keys = array_keys($array);
				$i = 0;
				foreach ($array as $pdata) {
					if (isset($pdata[$level])) {
						foreach ($pdata[$level] as $data) {
							if ($data["x1"] <= $pos->getFloorX() and $pos->getFloorX() <= $data["x2"]) {
								if ($data["z1"] <= $pos->getFloorZ() and $pos->getFloorZ() <= $data["z2"]) {
									if ($keys[$i] !== $n) {
										return false;
									}
								}
							}
						}
					}
					$i++;
				}
				return true;
				break;
		}
	}

	public static function getProtectinfo(Position $pos)
	{
		$level = $pos->getLevel()->getName();
		$array = main::getMain()->getProtect()->getAll();
		$keys = array_keys($array);
		$i = 0;

		foreach ($array as $pdata) {
			if (isset($pdata[$level])) {
				foreach ($pdata[$level] as $data) {
					if ($data["x1"] <= $pos->getFloorX() and $pos->getFloorX() <= $data["x2"]) {
						if ($data["z1"] <= $pos->getFloorZ() and $pos->getFloorZ() <= $data["z2"]) {
							return ReefAPI::BAD . $keys[$i] . "の土地です";
						}
					}
				}
			}
			$i++;
		}
		return false;
	}

	/**
	 * @param Item $item
	 * @return bool
	 */
	public static function isBanitem(Item $item): bool
	{
		$id = $item->getId();
		$meta = $item->getDamage();

		switch ($id) {
			case Item::BUCKET:
				switch ($meta) {
					case 10:
						return false;
				}
		}
		return true;
	}

	/**
	 * @param Position $pos1
	 * @param Position $pos2
	 * @param Player|NULL $p
	 * @return bool
	 */
	public static function addProtect(Position $pos1, Position $pos2, Player $p): bool
	{
		$config = main::getMain()->getProtect();
		$n = "§aReef§bProtect";
		$x = $pos2->getFloorX() - $pos1->getFloorX();
		$z = $pos2->getFloorZ() - $pos1->getFloorZ();
		$count = $x * $z;
		if ($p instanceof Player) {
			if ($pos1->getLevel() !== $pos2->getLevel()) {
				$p->sendMessage(self::ERROR . "ワールドをまたいでいます");
				return false;
			}
			if ($pos1->getLevel()->getName() !== "leveling_2") {
				$p->sendMessage(self::BAD . "このワールドは土地保護ができません");
				return false;
			}
			$n = $p->getName();
			if ($config->exists($n)) {
				$pdata = $config->get($n);
				$i = 0;
				foreach ($pdata as $world) {
					foreach ($world as $data) {
						$x = $data["x2"] - $data["x1"];
						$z = $data["z2"] - $data["z1"];
						$protect = $x * $z;
						$count = $count + $protect;
						$i++;
					}
				}
				if ($i >= 3) {
					$p->sendMessage(self::BAD . "3個以上の土地保護はできません");
					return false;
				}
				if ($count >= 10000) {
					$p->sendMessage(self::BAD . "合計で10000ブロック以上の保護は出来ません");
					return false;
				}
			}
		}
		$protect = $config->getAll();
		$p->sendMessage(self::GOOD . "現在処理しています...");
		main::getMain()->getScheduler()->scheduleDelayedTask(new ReefProtectTask($pos1, $pos2, $protect, $p) ,20);
		return true;
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public static function removeProtect(int $id): bool
	{
		$array = main::getMain()->getProtect()->getAll();
		$akeys = array_keys($array);
		$aNum = 0;
		foreach ($array as $pdata) {
			$pkeys = array_keys($pdata);
			$pNum = 0;
			foreach ($pdata as $world) {
				$wkeys = array_keys($world);
				$wNum = 0;
				foreach ($world as $data) {
					if ($data['id'] === $id) {
						unset($world[$wkeys[$wNum]]);
						$pdata[$pkeys[$pNum]] = $world;
						$array[$akeys[$aNum]] = $pdata;
						main::getMain()->getProtect()->setAll($array);
						main::getMain()->getProtect()->save();
						return true;
					}
					$wNum++;
				}
				$pNum++;
			}
			$aNum++;
		}
		return false;
	}

	/**
	 * @param string $n
	 * @param int $id
	 * @return bool
	 */
	public static function addProtectShare(string $n, int $id): bool
	{
		$array = main::getMain()->getProtect()->getAll();
		$akeys = array_keys($array);
		$aNum = 0;
		foreach ($array as $pdata) {
			$pkeys = array_keys($pdata);
			$pNum = 0;
			foreach ($pdata as $world) {
				$wkeys = array_keys($world);
				$wNum = 0;
				foreach ($world as $data) {
					if ($data['id'] === $id) {
						if (in_array($n, $data['sub'])) {
							return false;
						} else {
							$data['sub'][] = $n;
							$world[$wkeys[$wNum]] = $data;
							$pdata[$pkeys[$pNum]] = $world;
							$array[$akeys[$aNum]] = $pdata;
							main::getMain()->getProtect()->setAll($array);
							main::getMain()->getProtect()->save();
							return true;
						}
					}
					$wNum++;
				}
				$pNum++;
			}
			$aNum++;
		}
		return false;
	}

	/**
	 * @param string $n
	 * @param int $id
	 * @return bool
	 */
	public static function removeProtectShare(string $n, int $id): bool
	{
		$array = main::getMain()->getProtect()->getAll();
		$akeys = array_keys($array);
		$aNum = 0;
		foreach ($array as $pdata) {
			$pkeys = array_keys($pdata);
			$pNum = 0;
			foreach ($pdata as $world) {
				$wkeys = array_keys($world);
				$wNum = 0;
				foreach ($world as $data) {
					if ($data['id'] === $id) {
						if(($key = array_search($n, $data['sub'])) === false) {
							return false;
						}else{
							unset($data['sub'][$key]);
							$world[$wkeys[$wNum]] = $data;
							$pdata[$pkeys[$pNum]] = $world;
							$array[$akeys[$aNum]] = $pdata;
							main::getMain()->getProtect()->setAll($array);
							main::getMain()->getProtect()->save();
							return true;
						}
					}
					$wNum++;
				}
				$pNum++;
			}
			$aNum++;
		}
		return false;
	}

	/**
	 * @param $string
	 */
	public static function setOpen(string $string, string $name = "ReefNetWork_Core"): void
	{
		if ($string === "true") {
			$string = true;
		}
		main::getMain()->setOpen($string);
		Server::getInstance()->getNetWork()->setName("§kxX§r §aReef §eNetWork §cClose §r§kXx§r");

		if ($string === true) {
			$string = "access可能";
			Server::getInstance()->getNetWork()->setName("§kxX§r §aReef §eNetWork §bOpen §r§kXx§r");
		}
		$webhook = main::getMain()->getWebHook();
		$content = new Content();
		$webhook->add($content);
		$embeds = new Embeds();
		$embed = new Embed();
		$embed->addField("Operation", $name);
		$embed->addField("Status", $string);
		$embeds->add($embed);
		$webhook->add($embeds);
		$webhook->setCustomName("Access");
		Sender::sendAsync($webhook);
	}

	/**
	 * @return Webhook
	 */
	public static function getWebhook(int $type = 0)
	{
		return main::getMain()->getWebHook($type);
	}
}