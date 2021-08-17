<?php


namespace Ree\reef;


use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class ReefProtectTask extends Task
{
	/**
	 * @var int
	 */
	private $sx;
	/**
	 * @var int
	 */
	private $hx;
	/**
	 * @var int
	 */
	private $sz;
	/**
	 * @var int
	 */
	private $hz;
	/**
	 * @var array
	 */
	private $protect;
	/**
	 * @var string
	 */
	private $level;
	/**
	 * @var string
	 */
	private $player;

	public function __construct(Position $pos1, Position $pos2, array $protect, Player $p)
	{
		if ($pos1->getFloorX() <= $pos2->getFloorX()) {
			$sx = $pos1->getFloorX();
			$hx = $pos2->getFloorX();
		} elseif ($pos1->getFloorX() >= $pos2->getFloorX()) {
			$sx = $pos2->getFloorX();
			$hx = $pos1->getFloorX();
		} else {
			$p->sendMessage(ReefAPI::ERROR . "土地保護システムの情報が読み込めませんでした");
			return;
		}
		if ($pos1->getFloorZ() <= $pos2->getFloorZ()) {
			$sz = $pos1->getFloorZ();
			$hz = $pos2->getFloorZ();
		} elseif ($pos1->getFloorZ() >= $pos2->getFloorZ()) {
			$sz = $pos2->getFloorZ();
			$hz = $pos1->getFloorZ();
		} else {
			$p->sendMessage(ReefAPI::ERROR . "土地保護システムの情報が読み込めませんでした");
			return;
		}
		$this->hx = $hx;
		$this->sx = $sx;
		$this->hz = $hz;
		$this->sz = $sz;
		$this->protect = $protect;
		$this->level = $pos1->getLevel()->getName();
		$this->player = $p->getName();
	}

	public function onRun(int $currentTick)
	{
		$p = Server::getInstance()->getPlayer($this->player);
		if (!$p)
		{
			return;
		}
		$n = $p->getName();
		$array = $this->protect;
		$sx = $this->sx;
		$hx = $this->hx;
		$sz = $this->sz;
		$hz = $this->hz;
		for ($x = $sx; $x <= $hx; $x++) {
			for ($z = $sz; $z <= $hz; $z++) {
				if ($array !== []) {
					foreach ($array as $pdata) {
						if (isset($pdata[$this->level])) {
							foreach ($pdata[$this->level] as $data) {
								for ($testx = $data["x1"]; $testx <= $data["x2"]; $testx++) {
									for ($testz = $data["z1"]; $testz <= $data["z2"]; $testz++) {
										if ($data["x1"] <= $x and $x <= $data["x2"]) {
											if ($data["z1"] <= $z and $z <= $data["z2"]) {
												if ($testx <= $x and $x <= $testx) {
													if ($testz <= $z and $z <= $testz) {
														$p->sendMessage(ReefAPI::BAD ."x:".$x." z:".$z. "の土地がすでに保護されています");
														return;
													}
												}
											}
										}
									}
								}

							}
						}
					}
				}
			}
		}
		$pdata = main::getMain()->getProtect()->get($n);
		if (isset($pdata[$this->level])) {
			$world = $pdata[$this->level];
		} else {
			$world = [];
		}
		$data["x1"] = $this->sx;
		$data["z1"] = $this->sz;
		$data["x2"] = $this->hx;
		$data["z2"] = $this->hz;
		$data["id"] = main::getMain()->creatProtectNumber();
		$data["sub"] = [];
		$data["entityCount"] = 0;
		$world[] = $data;
		$pdata[$this->level] = $world;
		main::getMain()->getProtect()->set($n, $pdata);
		main::getMain()->getProtect()->save();

		$p->sendMessage(ReefAPI::GOOD . "土地の購入に成功しました");
	}
}