<?php


namespace Ree\plugin\cape;


use pocketmine\Server;
use Ree\reef\main;

class CapeSystem
{
	const GOOD = "CapePlugin §a>> §r";
	const BAD = "CapePlugin §6>> §r";
	const ERROR = "CapePlugin §c>> §r";

	/**
	 * @var CapeSystem
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
		self::$instance = new CapeSystem();
		Server::getInstance()->getPluginManager()->registerEvents(new CapeListener() ,$main);
		$main->getLogger()->info(self::GOOD.'loaded');
	}
}