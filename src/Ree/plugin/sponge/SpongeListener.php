<?php


namespace Ree\plugin\sponge;


use pocketmine\block\Block;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\Server;
use Ree\seichi\main;

class SpongeListener implements Listener
{
	public function onPlace(BlockPlaceEvent $ev)
	{
		$block = $ev->getBlock();
		$vector3 = $block->asVector3();
		$level = $block->getLevel();
		$pT = main::getpT($ev->getPlayer()->getName());
		$bool = false;

		if ($block->getId() === Block::SPONGE and $block->getDamage() === 0)
		{
			foreach ($this->getSpace($vector3) as $pos)
			{
				$id = $level->getBlock($pos)->getId();
				if ($id === Block::FLOWING_WATER or $id === Block::WATER)
				{
					$level->setBlock($pos ,Block::get(Block::AIR));
					$pT->addxp($pos);
					$bool = true;
				}
			}
		}
		if ($bool)
		{
			\Ree\reef\main::getMain()->getScheduler()->scheduleDelayedTask(new SpongeReplaceTask($vector3 ,$level) ,1);
		}
	}

	private function getSpace(Vector3 $vector3): array
	{
		$space = [];
		for ($x = -5 ;$x <= 5 ;$x++)
		{
			for ($y = -5 ;$y <= 5 ;$y++)
			{
				for ($z = -5 ;$z <= 5;$z++)
				{
					$space[] = $vector3->add($x ,$y ,$z);
				}
			}
		}
		return $space;
	}
}