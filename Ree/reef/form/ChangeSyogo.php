<?php

namespace Ree\reef\form;

use pocketmine\Player;
use Ree\reef\ReefAPI;
use Ree\seichi\form\MenuForm;
use Ree\seichi\main;

class ChangeSyogo implements \pocketmine\form\Form
{
	/**
	 * @var Player
	 */
	private $p;
	/**
	 * @var string[]
	 */
	private $list;
	/**
	 * @var int
	 */
	private $num;
	public function __construct(Player $p ,int $num)
	{
		$this->p = $p;
		$this->num = $num;
	}

	public function jsonSerialize()
	{
		foreach (\Ree\reef\main::getMain()->getSyogolist()->get($this->p->getName()) as $string)
		{
			$buttons[] = [
				'text' => $string
			];
			$this->list[] = $string;
		}
		$buttons[] = [
			'text' => "戻る"
		];
		$num = $this->num - 1;
		return [
			'type' => 'form',
			'title' => '称号',
			'content' => '現在の称号' .$this->num."\n" . \Ree\reef\main::getMain()->getSyogo()->get($this->p->getName())[$num],
			'buttons' => $buttons
		];
	}

	public function handleResponse(Player $p, $data): void
	{
		$pT = main::getpT($p->getName());
		$num = $this->num - 1;
		if ($data === NULL) {
			return;
		}
		if (isset($this->list[$data]))
		{
			$array = \Ree\reef\main::getMain()->getSyogo()->get($p->getName());
			$array[$num] = $this->list[$data];
			\Ree\reef\main::getMain()->getSyogo()->set($p->getName() ,$array);
			\Ree\reef\main::getMain()->getSyogo()->save();
			$p->sendForm(new SyogoForm($p ,"称号を".$this->list[$data]."§rに変更しました\n"));
		}else{
			$p->sendForm(new MenuForm($pT));
		}
		ReefAPI::UpdateSyogo($p);
	}
}