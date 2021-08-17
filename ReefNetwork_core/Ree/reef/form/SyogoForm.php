<?php

namespace Ree\reef\form;

use pocketmine\form\Form;
use pocketmine\Player;
use Ree\reef\ReefAPI;
use Ree\seichi\form\MenuForm;
use Ree\seichi\main;

class SyogoForm implements Form
{
	/**
	 * @var Player
	 */
	private $p;
	/**
	 * @var string
	 */
	private $string;
	public function __construct(Player $p ,string $string = "")
	{
		$this->p = $p;
		$this->string = $string;
	}

	public function jsonSerialize()
    {
        return [
            'type' => 'form',
            'title' => '称号変更',
            'content' => $this->string.'現在の称号'."\n".ReefAPI::getSyogo($this->p),
            'buttons' => [
				[
					'text' => "称号1を変更する"
				],
				[
					'text' => "称号2を変更する"
				],
				[
					'text' => "称号3を変更する"
				],
                [
                    'text' => "戻る"
                ],
            ]
        ];
    }

    public function handleResponse(Player $p, $data): void
    {
        // TODO: Implement handleResponse() method.
		$pT = main::getpT($p->getName());
        if ($data === NULL) {
            return;
        }
        switch ($data) {
			case 0:
				$p->sendForm(new ChangeSyogo($p ,1));
				break;

			case 1:
				$p->sendForm(new ChangeSyogo($p ,2));
				break;

			case 2:
				$p->sendForm(new ChangeSyogo($p ,3));
				break;

            case 3:
                $p->sendForm(new MenuForm($pT));
                break;
        }
    }
}