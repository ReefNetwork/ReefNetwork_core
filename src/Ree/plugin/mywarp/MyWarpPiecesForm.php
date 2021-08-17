<?php


namespace Ree\plugin\mywarp;


use pocketmine\form\Form;
use pocketmine\Player;

class MyWarpPiecesForm implements Form
{
	private const BACK = 'back';

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
		if ($data === NULL) {
			return;
		}
		if ($data === self::BACK) {

			return;
		}
		switch ($data) {
			case 0:
				$pos = MyWarpAPI::convertPoint($this->data);
				$player->sendMessage(MyWarpAPI::GOOD . 'テレポートしています');
				$player->teleport($pos);
				break;

			case 1:
				$player->sendForm(new MyWarpRemoveForm($this->data));
				break;

			case self::BACK:
				$player->sendForm(new MyWarpForm($player));
				break;
		}
	}

	/**
	 * @inheritDoc
	 */
	public
	function jsonSerialize()
	{
		return [
			'type' => 'form',
			'title' => 'マイワープ',
			'content' => "",
			'buttons' => [
				[
					'text' => "テレポートする"
				],
				[
					'text' => "地点を削除する"
				],
				[
					'text' => "戻る"
				],
			]
		];
	}
}