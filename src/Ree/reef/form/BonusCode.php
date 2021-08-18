<?php


namespace Ree\reef\form;


use pocketmine\form\Form;
use pocketmine\item\Item;
use pocketmine\Player;
use Ree\reef\main;
use Ree\reef\ReefAPI;
use Ree\seichi\Gatya;
use Ree\seichi\PlayerTask;

class BonusCode implements Form
{
	/**
	 * @var array
	 */
	private $array;

	public function jsonSerialize()
	{
		return [
			'type' => 'custom_form',
			'title' => 'コード入力',
			'content' => [
				[
					"type" => "input",
					"text" => "ボーナスコードを入力してください",
					"placeholder" => "string",
					"default" => ""
				],
			]
		];
	}

	public function handleResponse(Player $player, $data): void
	{
		if ($data === NULL) {
			return;
		}

		if (isset ($data[0])) {
			$code = mb_strtolower($data[0]);
			switch ($code) {
				case "discord500":
					$item = Gatya::getGatya(0, $player)->setCount(150);
					if (!$player->getInventory()->canAddItem($item)) {
						$player->sendMessage(ReefAPI::BAD . 'インベントリがいっぱいです');
						break;
					}
					if ($this->Use($player, $code)) {
						$player->sendMessage(ReefAPI::GOOD . 'ガチャ券を入手しました');
						$player->getInventory()->addItem($item);
					} else {
						$player->sendMessage(ReefAPI::BAD . 'そのコードはすでに使用されています');
					}
					break;

				default:
					$player->sendMessage(ReefAPI::BAD . 'そのようなコードは存在しません');
			}
		}
	}

	/**
	 * @param Player $player
	 * @param string $code
	 * @return bool
	 */
	private function Use(Player $player, string $code): bool
	{
		$data = main::getMain()->getSubData();
		if ($data->exists($code)) {
			$array = $data->get($code);
			if (in_array($player->getName(), $array)) {
				return false;
			}
		} else {
			$array = [];
		}
		$array[] = $player->getName();
		$data->set($code, $array);
		$data->save();
		return true;
	}

	private function addCoin(PlayerTask $pT, int $coin): bool
	{
		$pcoin = $pT->s_coin;
		$pT->s_coin = $pcoin + $coin;
		return true;
	}
}
