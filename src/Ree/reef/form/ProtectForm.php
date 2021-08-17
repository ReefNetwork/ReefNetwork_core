<?php

namespace Ree\reef\form;

use pocketmine\form\Form;
use pocketmine\level\Position;
use pocketmine\Player;
use Ree\reef\main;
use Ree\reef\ReefAPI;

class ProtectForm implements Form
{
	/**
	 * @var Position
	 */
	private $pos;
	/**
	 * @var Position
	 */
	private $start;
	/**
	 * @var Position
	 */
	private $finish;
	/**
	 * @var string
	 */
	private $string;
	/**
	 * @var bool
	 */
	private $bool = true;
	public function __construct(Position $pos ,$start ,$finish ,string $string = "")
	{
		$this->pos = $pos;
		$this->start = $start;
		$this->finish = $finish;
		$this->string = $string;
	}

	public function jsonSerialize()
    {
		$pos = $this->pos->getFloorX().' : '.$this->pos->getFloorZ();
    	if ($this->start)
		{
			$start = $this->start->getFloorX().' : '.$this->start->getFloorZ();
		}else{
			$start = "セットされていません";
			$this->bool = false;
		}
		if ($this->finish)
		{
			$finish = $this->finish->getFloorX().' : '.$this->finish->getFloorZ();
		}else{
			$finish = "セットされていません";
			$this->bool = false;
		}
        return [
            'type' => 'form',
            'title' => '土地保護システム',
            'content' => $this->string.'選択した地点 '.$pos."\n".'スタート地点 '.$start."\n".'最終地点 '.$finish,
            'buttons' => [
				[
					'text' => "スタート地点をセットする"
				],
				[
					'text' => "最終地点をセットする"
				],
				[
					'text' => "土地を買う"
				],
            ]
        ];
    }

    public function handleResponse(Player $p, $data): void
    {
    	$n = $p->getName();
        if ($data === NULL) {
            return;
        }
        switch ($data) {
			case 0:
				$list = main::getMain()->protectlist[$n];
				$list["start"] = $this->pos;
				main::getMain()->protectlist[$n] = $list;
				$p->sendMessage(ReefAPI::GOOD."スタート地点がセットされました");
				break;

			case 1:
				$list = main::getMain()->protectlist[$n];
				$list["finish"] = $this->pos;
				main::getMain()->protectlist[$n] = $list;
				$p->sendMessage(ReefAPI::GOOD."最終地点がセットされました");
				break;

			case 2:
				if ($this->bool)
				{
					ReefAPI::addProtect($this->start ,$this->finish ,$p);
				}else{
					$p->sendForm(new ProtectForm($this->pos ,$this->start ,$this->finish ,ReefAPI::BAD."地点がセットされていません\n"));
				}
				break;
        }
    }
}