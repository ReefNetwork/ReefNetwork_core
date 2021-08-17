<?php


namespace Ree\plugin\cape;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use Ree\reef\main;

class CapeListener implements Listener
{
	public function onJoin(PlayerJoinEvent $ev)
	{
		$p = $ev->getPlayer();
//		CapeAPI::updateCape($p);
	}
}