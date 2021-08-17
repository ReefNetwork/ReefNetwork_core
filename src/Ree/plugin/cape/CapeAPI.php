<?php


namespace Ree\plugin\cape;


use pocketmine\network\mcpe\protocol\types\Cape;
use pocketmine\network\mcpe\protocol\types\SkinImage;
use pocketmine\Player;

class CapeAPI implements CapeAPI_Base
{

	const NBT = 'capePass';

	/**
	 * @inheritDoc
	 */
	public static function updateCape(Player $p): void
	{
		$nbt = $p->namedtag;
		if ($nbt->offsetExists(self::NBT)) {
			$pass = $nbt->getString(self::NBT);
			$oldSkin = $p->getSkin();
			$img = imagecreatefrompng($pass);
			$capeData = "";
			$width  = imagesx($img);
			$height = imagesy($img);
			for($y = 0; $y < $height; $y++){
				for($x = 0; $x < $width; $x++){
					$rgba = imagecolorat($img, $x, $y);
					$a = ((~((int)($rgba >> 24))) << 1) & 0xff;
					$r = ($rgba >> 16) & 0xff;
					$g = ($rgba >> 8) & 0xff;
					$b = $rgba & 0xff;
					$capeData .= chr($r) . chr($g) . chr($b) . chr($a);
				}
			}
			@imagedestroy($img);
			$cape = new Cape('cape' ,new SkinImage($height ,$width ,$capeData));
			$oldSkin->setCape($cape);
			$p->sendSkin();
		}
	}

	/**
	 * @inheritDoc
	 */
	public static function setCapePass(Player $p, string $pass): void
	{
		$nbt = $p->namedtag;
		$nbt->setString(self::NBT ,$pass);
	}
}