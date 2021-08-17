<?php


namespace Ree\plugin\mywarp;


use pocketmine\level\Position;
use pocketmine\math\Vector3;

interface MyWarpAPI_Base
{
	/**
	 * @param string $n
	 * @param Vector3 $vec3
	 * @param $levelName
	 *
	 * add My Warp
	 */
	public static function addMyWarp(string $n , Vector3 $vec3 , $levelName): void ;

	/**
	 * @param string $n
	 * @param Vector3 $vec3
	 * @param string $levelName
	 * @return bool
	 *
	 * remove My Warp
	 * returns false if my warp does not exist
	 */
	public static function removeMyWarp(string $n , Vector3 $vec3 , $levelName): bool ;

	/**
	 * @param string $n
	 * @return array
	 *
	 * returns the position as an array
	 */
	public static function getMyWarps(string $n): array ;

	/**
	 * @param array $data
	 * @return Vector3
	 *
	 * Converts data stored in an array to Position
	 */
	public static function convertPoint(array $data): Position;
}