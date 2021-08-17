<?php


namespace Ree\plugin\mywarp;


use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;

class MyWarpAPI extends MyWarpSystem implements MyWarpAPI_Base
{

	/**
	 * @inheritDoc
	 */
	public static function addMyWarp(string $n, Vector3 $vec3, $levelName): void
	{
		$instance = self::getInstance();
		$data = $instance->getData($n);
		$array['x'] = $vec3->getX();
		$array['y'] = $vec3->getY();
		$array['z'] = $vec3->getZ();
		$array['level'] = $levelName;
		$data[] = $array;
		$instance->setData($n ,$data);
	}

	/**
	 * @inheritDoc
	 */
	public static function removeMyWarp(string $n, Vector3 $vec3, $levelName): bool
	{
		$instance = self::getInstance();
		$data = $instance->getData($n);
		$i = 0;
		foreach ($data as $warp)
		{
			if ($warp['x'] === $vec3->getX())
			{
				if ($warp['y'] === $vec3->getY())
				{
					if ($warp['z'] === $vec3->getZ())
					{
						if ($warp['level'] === $levelName)
						{
							$keys = array_keys($data);
							unset($data[$keys[$i]]);
							$instance->setData($n ,$data);
							return true;
						}
					}
				}
			}
			$i++;
		}
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public static function getMyWarps(string $n): array
	{
		$instance = self::getInstance();
		return $instance->getData($n);
	}


	/**
	 * @inheritDoc
	 */
	public static function convertPoint(array $data): Position
	{
		$level = Server::getInstance()->getLevelByName($data['level']);
		return new Position($data['x'] ,$data['y'] ,$data['z'] ,$level);
	}
}