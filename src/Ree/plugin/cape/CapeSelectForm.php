<?php


namespace Ree\plugin\cape;


use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\Player;
use Ree\reef\main;

class CapeSelectForm implements Form
{

	/**
	 * @inheritDoc
	 */
	public function handleResponse(Player $player, $data): void
	{
		if ($data === NULL) {
			return;
		}
		switch ($data)
		{
			case 0:
				$pass = main::getMain()->getDataFolder().'turuhasi.png';
				CapeAPI::setCapePass($player ,$pass);
				CapeAPI::updateCape($player);
				break;

			case 1:
				$pass = main::getMain()->getDataFolder().'enderman.png';
				CapeAPI::setCapePass($player ,$pass);
				CapeAPI::updateCape($player);
				break;

			case 2:
				$pass = main::getMain()->getDataFolder().'go-remu.png';
				CapeAPI::setCapePass($player ,$pass);
				CapeAPI::updateCape($player);
				break;

			case 3:
				$pass = main::getMain()->getDataFolder().'redcreeper.png';
				CapeAPI::setCapePass($player ,$pass);
				CapeAPI::updateCape($player);
				break;

			case 4:
				$pass = main::getMain()->getDataFolder().'aaa.png';
				CapeAPI::setCapePass($player ,$pass);
				CapeAPI::updateCape($player);
				break;

			default:
				$player->sendMessage(CapeSystem::ERROR.'不明な値');
				break;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize()
	{
		return [
			'type' => 'form',
			'title' => 'ケープ',
			'content' => "",
			'buttons' => [
				[
					'text' => "つるはしマント"
				],
				[
					'text' => "エンダーマンマント"
				],
				[
					'text' => "ゴーレムマント"
				],
				[
					'text' => "赤いクリーパーマント"
				],
				[
					'text' => "分かんないやつ"
				],
			]
		];
	}
}