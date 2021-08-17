<?php


namespace Ree\plugin\mywarp;


use pocketmine\Server;
use pocketmine\utils\Config;
use Ree\reef\main;

class MyWarpSystem
{
	const GOOD = "MyWarpPlugin §a>> §r";
	const BAD = "MyWarpPlugin §6>> §r";
	const ERROR = "MyWarpPlugin §c>> §r";

	/**
	 * @var Config
	 */
	private $warpData;

	/**
	 * @var MyWarpSystem
	 */
	private static $instance;

	public static function load(main $main): void
	{
		if (self::$instance)
		{
			$main->getLogger()->error(self::ERROR.'Trying to call load twice');
			Server::getInstance()->getPluginManager()->disablePlugin($main);
			return;
		}
		self::$instance = new MyWarpSystem();
		self::getInstance()->warpData = new Config($main->getDataFolder() . "MyWarp.yml", Config::YAML);
		$main->getLogger()->info(self::GOOD.'loaded');
	}

	protected function getData(string $n): array
	{
		if (self::getInstance()->warpData->exists($n)) {
			return self::getInstance()->warpData->get($n);
		} else {
			return [];
		}
	}

	protected function setData(string $n, array $data): void
	{
		self::getInstance()->warpData->set($n, $data);
		self::getInstance()->warpData->save();
	}

	protected static function getInstance(): MyWarpSystem
	{
		return self::$instance;
	}
}