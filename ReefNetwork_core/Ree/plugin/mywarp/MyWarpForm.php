<?php


namespace Ree\plugin\mywarp;


use pocketmine\form\Form;
use pocketmine\Player;
use Ree\reef\ReefAPI;
use Ree\seichi\form\MenuForm;
use Ree\seichi\main;

class MyWarpForm implements Form
{
	private const ADD = 'add';
	private const BACK = 'back';

	/**
	 * @var array
	 */
	private $list;

	/**
	 * @var int
	 */
	private $count;

	/**
	 * @var Player
	 */
	private $p;

	public function __construct(Player $p)
	{
		$this->p = $p;
	}

	public function handleResponse(Player $player, $data): void
	{
		if ($data === NULL) {
			return;
		}
		if (isset($this->list[$data]))
		{
			$value = $this->list[$data];
			if ($value === self::ADD)
			{
				if (!ReefAPI::isVip($player->getName()) and $this->count >= 3)
				{
					$player->sendMessage(MyWarpAPI::BAD.'3個以上のワープ地点の作成は出来ません');
					return;
				}
				$pos = $player->asVector3();
				MyWarpAPI::addMyWarp($player->getName() ,$pos ,$player->getLevel()->getName());
				$player->sendMessage(MyWarpAPI::GOOD.'ワープ地点を作成しました');
				return;
			}
			if ($value === self::BACK)
			{
				$pT = main::getpT($player->getName());
				$player->sendForm(new MenuForm($pT));
				return;
			}
			$player->sendForm(new MyWarpPiecesForm($value));
		}else{
			$player->sendMessage(MyWarpSystem::ERROR.'不正な値が検出されました');
		}
	}

	public function jsonSerialize()
	{
		$array = MyWarpAPI::getMyWarps($this->p->getName());
		$buttons = [];
		$n = 0;
		foreach ($array as $data)
		{
			$buttons[] = [
				'text' => 'ワールド : '.$data['level']."\n".$data['x'] . ' '.$data['y'] . ' '.$data['z']
			];
			$this->list[] = $data;
			$n++;
		}
		$buttons[] = [
			'text' => "地点を追加する"
		];
		$buttons[] = [
			'text' => "戻る"
		];
		$this->list[] = self::ADD;
		$this->list[] = self::BACK;

		$this->count = $n;

		return [
			'type' => 'form',
			'title' => 'マイワープ',
			'content' => '',
			'buttons' => $buttons
		];
	}
}