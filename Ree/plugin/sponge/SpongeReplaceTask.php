<?php


namespace Ree\plugin\sponge;


use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;

class SpongeReplaceTask extends Task
{
	/**
	 * @var Vector3
	 */
	private $vector3;
	/**
	 * @var Level
	 */
	private $level;
	public function __construct(Vector3 $vector3 ,Level $level)
	{
		$this->vector3 = $vector3;
		$this->level = $level;
	}

	/**
	 * @inheritDoc
	 */
	public function onRun(int $currentTick)
	{
		$block = $this->level->getBlock($this->vector3);
		if ($block->getId() === Block::SPONGE and $block->getDamage() === 0)
		{
			$this->level->setBlock($this->vector3 ,Block::get(Block::SPONGE ,1));
		}
	}
}