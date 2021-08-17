<?php


namespace Ree\plugin\cape;


use pocketmine\Player;

interface CapeAPI_Base
{
	/**
	 * @param Player $p
	 */
	public static function updateCape(Player $p): void;

	/**
	 * @param Player $p
	 * @param string $pass
	 */
	public static function setCapePass(Player $p, string $pass): void;
}