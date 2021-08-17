<?php


namespace Ree\plugin\clean_up;


use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Ree\seichi\main;

class CleanUpTask extends Task
{
	/**
	 * @var int
	 */
	private $time = 0;
	/**
	 * @var int
	 */
	private $warnig = 1;

	const STOP = 7200;

	const CLEAN = 900;

	public function onRun(int $currentTick)
	{
		$this->time++;
		if ($this->time >= self::STOP) {
			Server::getInstance()->broadcastMessage(CleanUpSystem::GOOD . "再起動しています");
		}
		if ($this->time > self::STOP) {
			foreach (Server::getInstance()->getOnlinePlayers() as $p) {
				$p->kick('§aReef§eNetwork§r' . "\n\n" . CleanUpSystem::GOOD . "再起動しています");
			}
			Server::getInstance()->shutdown();
		}
		switch (self::STOP - $this->time) {
			case 900:
				Server::getInstance()->broadcastMessage(CleanUpSystem::GOOD . '再起動まで15分です');
				break;

			case 600:
				Server::getInstance()->broadcastMessage(CleanUpSystem::GOOD . '再起動まで10分です');
				break;

			case 300:
				Server::getInstance()->broadcastMessage(CleanUpSystem::GOOD . '再起動まで5分です');
				break;

			case 60:
				Server::getInstance()->broadcastMessage(CleanUpSystem::GOOD . '再起動まで1分です');
				break;

			case 30:
				Server::getInstance()->broadcastMessage(CleanUpSystem::GOOD . '再起動まで30秒です');
				break;
		}

		if ($this->time % self::CLEAN === 0) {
			$this->warnig = 30;
			Server::getInstance()->broadcastMessage(CleanUpSystem::GOOD . 'エンティティ削除まで30秒です');
		}
		if ($this->warnig > 0) {
			switch ($this->warnig) {
				case 1:
					foreach (Server::getInstance()->getLevels() as $level) {
						foreach ($level->getEntities() as $entity) {
							if ($entity instanceof ItemEntity or $entity instanceof ExperienceOrb) {
								$entity->close();
							}
						}
					}
					$start = microtime(true);
					Server::getInstance()->broadcastMessage(CleanUpSystem::GOOD . 'エンティティを削除しました');
					$this->Clean();
					Server::getInstance()->broadcastMessage(CleanUpSystem::GOOD . 'サーバーのデータをセーブしています');
					$this->Save();
					Server::getInstance()->broadcastMessage(CleanUpSystem::GOOD .'全ての処理を'. round(microtime(true) - $start ,10) . '秒で完了しました');
					break;

				case 4:
					Server::getInstance()->broadcastMessage(CleanUpSystem::GOOD . 'エンティティ削除まで3秒です');
					break;

				case 11:
					Server::getInstance()->broadcastMessage(CleanUpSystem::GOOD . 'エンティティ削除まで10秒です');
					break;
			}
			$this->warnig--;
		}
	}

	public function getTime(): int
	{
		return self::STOP - $this->time;
	}

	public function getName(): string
	{
		return parent::getName();
	}

	public function onCancel()
	{
		parent::onCancel();
	}

	private function Save(): void
	{
		foreach (Server::getInstance()->getLevels() as $level) {
			$level->save(true);
		}

		foreach (Server::getInstance()->getOnlinePlayers() as $p) {
			$p->save();
			$pT = main::getpT($p->getName());
			main::getMain()->Save($pT);
		}
	}

	private function Clean(): void
	{
//		Server::getInstance()->getAsyncPool()->shutdownUnusedWorkers();
//		Server::getInstance()->getAsyncPool()->shutdown();
	}
}