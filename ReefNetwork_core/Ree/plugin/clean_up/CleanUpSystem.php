<?php


namespace Ree\plugin\clean_up;


use pocketmine\Server;
use Ree\reef\main;

class CleanUpSystem
{
	const GOOD = "CleanUpPlugin §a>> §r";
	const BAD = "CleanUpPlugin §6>> §r";
	const ERROR = "CleanUpPlugin §c>> §r";

	/**
	 * @var CleanUpSystem
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
		self::$instance = new CleanUpSystem();
		$main->getScheduler()->scheduleRepeatingTask(new CleanUpTask() ,20);
		$main->getLogger()->info(self::GOOD.'loaded');
	}
}