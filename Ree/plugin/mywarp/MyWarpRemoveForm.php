<?php


namespace Ree\plugin\mywarp;


use pocketmine\form\Form;
use pocketmine\level\Position;
use pocketmine\Player;

class MyWarpRemoveForm implements Form
{
	/**
	 * @var array
	 */
	private $data;
	public function __construct(array $data)
	{
		$this->data = $data;
	}

	/**
	 * @inheritDoc
	 */
	public function handleResponse(Player $player, $data): void
	{
		if ($data === null)
		{
			return;
		}
		$pos = MyWarpAPI::convertPoint($this->data);
		if ($data)
		{
			$bool = MyWarpAPI::removeMyWarp($player->getName(), $pos, $pos->getLevel()->getName());
			if ($bool) {
				$player->sendMessage(MyWarpAPI::GOOD . '地点を削除しました');
			} else {
				$player->sendMessage(MyWarpAPI::ERROR . '地点を削除出来ませんでした');
			}
		}else{
			$player->sendForm(new MyWarpPiecesForm($this->data));
		}
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize()
	{
		return [
			'type' => 'modal',
			'title' => 'スキル選択',
			'content' => "本当にワープ地点を削除しますか?",
			"button1" => "削除する",
			"button2" => "戻る",
		];
	}
}