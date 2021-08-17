<?php

namespace Ree\plugin\sponge;


use pocketmine\Server;
use Ree\reef\main;

class SpongeSystem
{
	const GOOD = "SpongePlugin §a>> §r";
	const BAD = "SpongePlugin §6>> §r";
	const ERROR = "SpongePlugin §c>> §r";

	/**
	 * @var SpongeSystem
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
		self::$instance = new SpongeSystem();
		Server::getInstance()->getPluginManager()->registerEvents(new SpongeListener() ,$main);
		$main->getLogger()->info(self::GOOD.'loaded');
	}
}