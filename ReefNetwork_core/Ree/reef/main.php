<?php


/*
 * _____            __ _   _      _                      _
 *|  __ \          / _| \ | |    | |                    | |
 *| |__) |___  ___| |_|  \| | ___| |___      _____  _ __| | __
 *|  _  // _ \/ _ \  _| . ` |/ _ \ __\ \ /\ / / _ \| '__| |/ /
 *| | \ \  __/  __/ | | |\  |  __/ |_ \ V  V / (_) | |  |   <
 *|_|  \_\___|\___|_| |_| \_|\___|\__| \_/\_/ \___/|_|  |_|\_\
 *
 * ReefNetwork_core
 * @copyright 2019 Ree_jp
 */


namespace Ree\reef;


use bboyyu51\pmdiscord\connect\Webhook;
use bboyyu51\pmdiscord\Sender;
use bboyyu51\pmdiscord\structure\Content;
use bboyyu51\pmdiscord\structure\Embeds;
use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\ItemFrameDropItemPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\utils\Config;
use Ree\plugin\cape\CapeSystem;
use Ree\plugin\clean_up\CleanUpSystem;
use Ree\plugin\mywarp\MyWarpSystem;
use Ree\plugin\socket\ReceiveTask;
use Ree\plugin\sponge\SpongeSystem;
use Ree\reef\form\ProtectForm;
use Ree\seichi\form\MenuForm;
use Ree\seichi\form\SyogoShopCheckForm;

class main extends PluginBase implements Listener
{
	/**
	 * @var Config
	 */
	public $banname;

	/**
	 * @var Config
	 */
	public $banip;

	/**
	 * @var $this
	 */
	private static $main;

	/**
	 * @var Config
	 */
	public $data;

	/**
	 * @var Config
	 */
	private $syogo;

	/**
	 * @var Config
	 */
	private $syogolist;

	/**
	 * @var Config
	 */
	private $protect;

	/**
	 * @var Config
	 */
	private $subdata;

	/**
	 * @var Config
	 */
	private $vip;

	/**
	 * @var Webhook
	 */
	private $webhook;

	/**
	 * @var Webhook
	 */
	private $chat;

	/**
	 * @var Webhook
	 */
	private $errer;

	/**
	 * @var array
	 */
	public $protectlist;

	/**
	 * @var ReceiveTask
	 */
	public static $receiveTask;

	private $open = true;

	public function onEnable()
	{
		echo 'ReefNetwork_core >> loading now...' . "\n";
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		self::$main = $this;
		$this->webhook = Sender::create("https://discordapp.com/api/webhooks/644834688283967518/V6aDcPKNN3BkOvo2jypqjyb3M-ti6JUmGJfmPzfU6r1a_2nbb89dfBBOG53j1gd1gnRa");
		$this->chat = Sender::create("https://discordapp.com/api/webhooks/644844397577633804/ixOiauLaL8Ea3sG23kCGuW7KGuebRlEqaWMdU2u5sFvn8l2tO0I4yAeuD_rvfVA8zI-s");
		$this->errer = Sender::create("https://discordapp.com/api/webhooks/645173075410681856/juykKQIeN3dTyxVICTvjA4Ne8hiAWQhXuV0FU0693b13V4jHYjiSUrsr2V0wgoIRY_wu");
		$this->banname = new Config($this->getDataFolder() . "ReefBanName.yml", Config::YAML);
		$this->banip = new Config($this->getDataFolder() . "ReefBanIp.yml", Config::YAML);
		$this->data = new Config($this->getDataFolder() . "ReefData.yml", Config::YAML);
		$this->syogo = new Config($this->getDataFolder() . "ReefSyogo.yml", Config::YAML);
		$this->syogolist = new Config($this->getDataFolder() . "ReefSyogoList.yml", Config::YAML);
		$this->protect = new Config($this->getDataFolder() . "ReefProtect.yml", Config::YAML);
		$this->subdata = new Config($this->getDataFolder() . "ReefManyData.yml", Config::YAML);
		$this->vip = new Config($this->getDataFolder() . "ReefVipData.yml", Config::YAML);

		$shape = array(
			"sss",
			"sss",
			"sss",
		);
		$output = array(
			Item::get(44, 0, 18),
		);
		$input = array(
			"s" => Item::get(1, 0, 1),
		);
		$recipe = new ShapedRecipe($shape, $input, $output);
		$recipe->registerToCraftingManager($this->getServer()->getCraftingManager());
		$input = array(
			"s" => Item::get(4, 0, 1),
		);
		$recipe = new ShapedRecipe($shape, $input, $output);
		$recipe->registerToCraftingManager($this->getServer()->getCraftingManager());

//		$task = new ReceiveTask();
//		$this->getServer()->getAsyncPool()->submitTask($task);
//		self::$receiveTask = $task;

		/**
		 * load reef network sub plugin
		 */
		echo 'load reef network sub plugin...' . "\n";
		MyWarpSystem::load($this);
		CleanUpSystem::load($this);
		SpongeSystem::load($this);
		CapeSystem::load($this);
		echo 'complete' . "\n";

		$this->getScheduler()->scheduleDelayedTask(new ServerStatusTask($this), 3);
		$this->getLogger()->info("§aReef§eNetwork §dEnable");
	}

	public function onDisable()
	{
		$this->getLogger()->info("§aReef§eNetwork §dDisable");

//		self::$receiveTask->close();

		$webhook = $this->getWebHook();
		$content = new Content();
		$content->setText("ServerStop");
		$webhook->add($content);
		$embeds = new Embeds();
		$webhook->add($embeds);
		$webhook->setCustomName("Stop");
		Sender::send($webhook);
		$this->banname->save();
		$this->banip->save();
		$this->data->save();
		$this->syogo->save();
		$this->syogolist->save();
		echo 'ReefNetwork_core >> Complete' . "\n";
	}

	public function onPreLogin(PlayerPreLoginEvent $ev)
	{
		$bool = ReefAPI::isBan($ev->getPlayer());
		if (!$bool) {

			$ev->getPlayer()->kick("§aReef§eNetwork\n§cBanned\nReason : ", false);
		}
		if ($this->open !== true) {
			if (!$ev->getPlayer()->isOp()) {
				$ev->getPlayer()->kick("§aReef§eNetwork\n\n§c" . $this->open, false);
			}
		}
	}

	public function onJoin(PlayerJoinEvent $ev)
	{
		$p = $ev->getPlayer();
		$n = $p->getName();

//        $p->teleport($p->getLevel()->getSafeSpawn());
		$p->setGamemode(0);

		if ($this->data->exists($n)) {
			$ev->setJoinMessage("§aJoin>>" . $n);
		} else {
			$ev->setJoinMessage("§bnewJoin>>" . $n);
			$this->data->set($n);
			$syogolist[] = "§a鯖民";
			$syogolist[] = "初";
			$syogolist[] = "心";
			$syogolist[] = "者";
			$this->syogolist->set($n, $syogolist);
			$syogo[0] = "初";
			$syogo[1] = "心";
			$syogo[2] = "者";
			$this->syogo->set($n, $syogo);
			$pos = new Position(0, 10, 0, $this->getServer()->getLevelByName("public"));
			$p->teleport($pos);
		}

		if (ReefAPI::isVip($n)) {
			$ev->setJoinMessage("§e[donor]§aJoin>>" . $n);
		}
		if ($p->isOp()) {
			$ev->setJoinMessage("§d[adomin]§aJoin>>" . $n);
		}
		switch ($n) {
			case "ScreenedHarp991":
				$ev->setJoinMessage("§b[owner]§aJoin>>" . $n);
				break;
		}

		$webhook = $this->chat;
		$content = new Content();
		$string = $this->onChange($ev->getJoinMessage());
		$content->setText($string);
		$webhook->add($content);
		$webhook->setCustomName("Join");
		Sender::sendAsync($webhook);

		$p->sendMessage("----§aReef§eNetWork§r----");
		$p->sendMessage("ブロックの破壊ログはすべて記録されています");
		$p->sendMessage("綺麗にに整地をお願いします");
		$p->sendMessage("分からないことがあればDiscordもしくはこちらまで");
		$p->sendMessage("http://reef.ree-jp.net/");
		$p->sendMessage("[お知らせ]アドレスがmc.ree-jp.netに変更されました");
		$p->sendMessage("[お知らせ]しばらくはmcbereef.ddo.jpでも参加可能ですが使えなくなる場合がありますので変更をお願いします");
		$p->sendMessage("-------------------");
	}

	public function onQuit(PlayerQuitEvent $ev)
	{
		$p = $ev->getPlayer();
		$n = $p->getName();

		switch ($ev->getQuitReason()) {
			case "Internal server error":
				$ev->setQuitMessage("§cInternal Server Error<<" . $n . "\n鯖主に連絡してください");
				$webhook = $this->getWebHook();
				$embeds = new Embeds();
				$content = new Content();
				$content->setText("-------------------");
				$webhook->add($content);
				Sender::sendAsync($webhook);
				$webhook->add($embeds);
				$webhook->setCustomName("ServerError");
				$content = new Content();
				$content->setText("Internal Server Error");
				$webhook->add($content);
				Sender::sendAsync($webhook);
				$content = new Content();
				$content->setText("Player : " . $p->getName());
				$webhook->add($content);
				Sender::sendAsync($webhook);
				$content->setText("-------------------");
				$webhook->add($content);
				Sender::sendAsync($webhook);

				$webhook = $this->errer;
				$webhook->setCustomName("ServerError");
				$content = new Content();
				$content->setText("-------------------");
				$webhook->add($content);
				Sender::send($webhook);
				$content = new Content();
				$content->setText("Player : " . $p->getName());
				$webhook->add($content);
				Sender::send($webhook);
				$content = new Content();
				$string = "今度表示されるようにする";
				$content->setText("PlayerTask : " . $string);
				$webhook->add($content);
				Sender::send($webhook);
				$content = new Content();
				$content->setText("-------------------");
				$webhook->add($content);
				Sender::send($webhook);
				break;
			case "client disconnect":
				$ev->setQuitMessage("§eQuit<<" . $n);
				break;
			default:
				$ev->setQuitMessage("§cUnknown[" . $ev->getQuitReason() . "§c]<<" . $n);

		}

		if ($p->isOp()) {
			switch ($ev->getQuitReason()) {
				case "Internal server error":
					$ev->setQuitMessage("§d[adomin]§cInternal Server Error<<" . $n . "\n鯖主に連絡してください");
					break;
				case "client disconnect":
					$ev->setQuitMessage("§d[adomin]§eQuit<<" . $n);
					break;
				default:
					$ev->setQuitMessage("§d[adomin]§cUnknow[" . $ev->getQuitReason() . "§c]<<" . $n);

			}
		}
		switch ($n) {
			case "ScreenedHarp991":
				switch ($ev->getQuitReason()) {
					case "Internal server error":
						$ev->setQuitMessage("§b[owner]§cInternal Server Error<<" . $n . "\n鯖主に連絡してください");
						break;
					case "client disconnect":
						$ev->setQuitMessage("§b[owner]§eQuit<<" . $n);
						break;
					default:
						$ev->setQuitMessage("§b[owner]§cUnknown[" . $ev->getQuitReason() . "§c]<<" . $n);

				}
				break;
		}
		$webhook = $this->chat;
		$content = new Content();
		$string = $this->onChange($ev->getQuitMessage());
		$content->setText($string);
		$webhook->add($content);
		$webhook->setCustomName("Quit");
		Sender::sendAsync($webhook);
	}

	public function onBreak(BlockBreakEvent $ev)
	{
		$p = $ev->getPlayer();
		$n = $p->getName();
		$pos = $ev->getBlock()->asPosition();

		$bool = ReefAPI::isProtect($pos, $p);
		if (!$bool) {
			if (!$p->isOp()) {
				$ev->setCancelled();
			}
			$string = ReefAPI::getProtectinfo($pos);
			if ($string) {
				$p->addActionBarMessage($string);
			} else {
				$p->addActionBarMessage(ReefAPI::BAD . "その場所のブロックを破壊することは出来ません");
			}
		}
	}

	public function onPlace(BlockPlaceEvent $ev)
	{
		$p = $ev->getPlayer();
		$n = $p->getName();
		$pos = $ev->getBlock()->asPosition();

		$bool = ReefAPI::isProtect($pos, $p);
		if (!$bool) {
			if (!$p->isOp()) {
				$ev->setCancelled();
			}
			$string = ReefAPI::getProtectinfo($pos);
			if ($string) {
				$p->addActionBarMessage($string);

			} else {
				$p->addActionBarMessage(ReefAPI::BAD . "その場所にブロックを設置することは出来ません");
			}
		}
	}

	public function onUpdate(BlockUpdateEvent $ev)
	{
		if ($ev->getBlock()->getId() == Block::FLOWING_WATER) {
			$ev->setCancelled();
		}
		if ($ev->getBlock()->getId() == Block::WATER) {
			$ev->setCancelled();
		}
	}

	/**
	 * @param PlayerInteractEvent $ev
	 */
	public function onTuch(PlayerInteractEvent $ev)
	{
		$p = $ev->getPlayer();
		$n = $p->getName();
		$pos = $ev->getBlock()->asPosition();

		$bool = ReefAPI::isBanitem($ev->getItem());
		if (!$bool) {
			if ($p->isOp()) {
				return;
			}
			$ev->setCancelled();
			$p->addActionBarMessage(ReefAPI::BAD . "そのアイテムは使用できません");
		}

		if ($ev->getItem()->getId() == Item::WOODEN_AXE) {
			if ($ev->getBlock()->getId() === Block::AIR) {
				$p->sendMessage(ReefAPI::ERROR . "土地保護システムの情報の収集に失敗しました");
				return;
			}
			$start = NULL;
			$finish = NULL;
			if (isset ($this->protectlist[$n])) {
				$list = $this->protectlist[$n];
				if (isset ($list["start"])) {
					$start = $list["start"];
				}
				if (isset ($list["finish"])) {
					$finish = $list["finish"];
				}
			} else {
				$this->protectlist[$n] = [];
			}
			$p->sendForm(new ProtectForm($pos, $start, $finish));
		}

		$bool = ReefAPI::isProtect($pos, $p);
		if (!$bool) {
			$string = ReefAPI::getProtectinfo($pos);
			if ($string) {
				$p->addActionBarMessage($string);
				if (!$p->isOp()) {
					$ev->setCancelled();
				}
			}
		}
	}

	public function onCommand(CommandSender $p, Command $command, string $label, array $args): bool
	{
		if ($command == "menu" or $command == "m") {
			if ($p instanceof Player) {
				$p->sendForm(new MenuForm(\Ree\seichi\main::getpT($p->getName())));
			} else {
				$p->sendMessage("プレイヤーのみ使用できます");
			}
		}

		if ($command == "reef") {
			$n = $p->getName();

			switch ($args[0]) {
				case 'buysyogo':
					if (!isset($args[3])) {
						return false;
					}

					if ($args[3] === '52vuh4uhn4ay') {
						$p->sendForm(new SyogoShopCheckForm($args[1], $args[2]));
						return true;
					}
			}

			if (!isset($args[0])) {
				$args[0] = "help";
			}
			if (!$p->isOp()) {
				return false;
			}
			switch ($args[0]) {
				case "nameban";
					if (!isset($args[1])) {
						return false;
					}
					if (ReefAPI::NameBan($args[1])) {
						$p->sendMessage("成功しました");
						$p = $this->getServer()->getPlayer($args[1]);
						if ($p) {
							$p->kick("§aReef§eNetwork\n\n§c   Banned", false);
						}
					} else {
						$p->sendMessage("失敗しました");
					}

					break;

				case "ipban":
					if (!isset($args[1])) {
						return false;
					}
					if (ReefAPI::IpBan($args[1])) {
						$p->sendMessage("成功しました");
						$p = $this->getServer()->getPlayer($args[1]);
						if ($p) {
							$p->kick("§aReef§eNetwork\n\n§c   Banned", false);
						}
					} else {
						$p->sendMessage("失敗しました");
					}
					break;

				case "cnameban":
					if (!isset($args[1])) {
						return false;
					}
					if (ReefAPI::CancelNameBan($args[1])) {
						$p->sendMessage("成功しました");
					} else {
						$p->sendMessage("失敗しました");
					}
					break;

				case "cipban":
					if (!isset($args[1])) {
						return false;
					}
					if (ReefAPI::CancelIpBan($args[1])) {
						$p->sendMessage("成功しました");
					} else {
						$p->sendMessage("失敗しました");
					}
					break;

				case "banlist":
					$p->sendMessage("-------nameban-------");
					foreach ($this->banname->getAll() as $name) {
						$p->sendMessage($name);
					}
					$p->sendMessage("---------------------");
					$p->sendMessage("--------ipban--------");
					foreach ($this->banip->getAll() as $ip) {
						$p->sendMessage($ip);
					}
					$p->sendMessage("---------------------");

					break;

				case "syogo":
					if (!isset($args[2])) {
						return false;
					}
					$p->sendForm(new SyogoShopCheckForm($args[1], $args[2]));
					break;

				case "addsyogo":
					if (!isset($args[2])) {
						return false;
					}
					$list = self::getMain()->getSyogolist()->get($args[1]);
					$list[] = $args[2];
					main::getMain()->setSyogo($args[1], $list);
					break;

				case "setopen":
					if (!isset($args[1])) {
						return false;
					}
					ReefAPI::setOpen($args[1], $n);
					$p->sendMessage("成功しました");
					return true;

				case "setnews":
					if (!isset($args[1])) {
						return false;
					}
					ReefAPI::$news = $args[1];
					break;

				case "vip":
					if (!isset($args[2])) {
						return false;
					}
					ReefAPI::setVip($args[1], $args[2]);
					$p->sendMessage(ReefAPI::GOOD . '成功しました');
					break;

				case "clear":
					Server::getInstance()->getAsyncPool()->shutdownUnusedWorkers();
					Server::getInstance()->getAsyncPool()->shutdown();
					Server::getInstance()->broadcastMessage(ReefAPI::GOOD.'非同期処理を強制終了させました');
					break;

				default:
					$p->sendMessage("Reef Operation ComandHelp");
					$p->sendMessage("------------------");
					$p->sendMessage("reef nameban <PlayerName>");
					$p->sendMessage("reef ipban <PlayerName>");
					$p->sendMessage("reef cnameban <PlayerName>");
					$p->sendMessage("reef cipban <PlayerName>");
					$p->sendMessage("reef banlist <PlayerName>");
					$p->sendMessage("reef syogo <string> <money>");
					$p->sendMessage("reef addsyogo <player> <string>");
					$p->sendMessage("reef setopen <reason|true>");
					$p->sendMessage("reef setnews <string>");
					$p->sendMessage("reef vip <PlayerName> <bool>");
					$p->sendMessage("reef clear");
					$p->sendMessage("------------------");
					return true;
			}
			return true;
		} else {
			return true;
		}
	}

	public function onDamage(EntityDamageEvent $ev)
	{
		$p = $ev->getEntity();
		if (!$p instanceof Player) {
			return;
		}
		$health = $p->getHealth();
		if ($ev instanceof EntityDamageByEntityEvent) {
			if ($ev->getDamager() instanceof Player) {
				$ev->setCancelled();
				return;
			}
		}
		switch ($ev->getCause()) {
			case EntityDamageEvent::CAUSE_FALL:
				$ev->setBaseDamage(0);
				break;

			case EntityDamageEvent::CAUSE_VOID:
				$health = -1;
				break;
		}
		if ($health <= $ev->getFinalDamage()) {
			$ev->setCancelled();
			$p->setHealth($p->getMaxHealth());
			$p->setFood($p->getMaxFood());
			$p->teleport($this->getServer()->getLevelByName("lobby")->getSafeSpawn());
			Server::getInstance()->broadcastMessage(ReefAPI::BAD . "§cDeath§r : " . $p->getName());
			$p->sendTip("§cYou are dead");
		}
	}

	public function onChat(PlayerChatEvent $ev)
	{
		$p = $ev->getPlayer();
		$webhook = $this->chat;
		$chat = $ev->getMessage();
		$chat = $this->onChange($chat);
		$content = new Content();
		$content->setText("`".$chat."`");
		$webhook->add($content);
		$n = $this->onChange(ReefAPI::getSyogo($p));
		$webhook->setCustomName($n);
		Sender::sendAsync($webhook);
	}

	public function onRecived(DataPacketReceiveEvent $ev)
	{
		$p = $ev->getPlayer();
		if ($ev->getPacket() instanceof ItemFrameDropItemPacket) {
			$pos = new Position($ev->getPacket()->x, $ev->getPacket()->y, $ev->getPacket()->z, $p->getLevel());
			$bool = ReefAPI::isProtect($pos, $p);
			if (!$bool) {
				$string = ReefAPI::getProtectinfo($pos);
				if ($string) {
					$p->addActionBarMessage($string);
					if (!$p->isOp()) {
						$ev->setCancelled();
					}
				}
			}
		}
	}

	/**
	 * @return Config
	 */
	public function getBanip(): Config
	{
		return $this->banip;
	}

	/**
	 * @return main
	 */
	public static function getMain()
	{
		return self::$main;
	}

	/**
	 * @return Config
	 */
	public function getSyogo()
	{
		return $this->syogo;
	}

	public function setSyogo(string $name, array $list): void
	{
		$this->syogolist->set($name, $list);
	}

	/**
	 * @return Config
	 */
	public function getSyogolist()
	{
		return $this->syogolist;
	}

	/**
	 * @param string $open
	 */
	public function setOpen($open)
	{
		$this->open = $open;
	}

	/**
	 * @param int $type
	 * @return Webhook
	 */
	public function getWebHook(int $type = 0)
	{
		switch ($type) {
			case 1:
				return $this->chat;

			case 0:
			default:
				return $this->webhook;
		}
	}

	/**
	 * @return Config
	 */
	public function getProtect(): Config
	{
		return $this->protect;
	}

	/**
	 * @param string $string
	 * @return string
	 */
	private function onChange(string $string)
	{
		$check = array("§1", "§2", "§3", "§4", "§5", "§6", "§7", "§8", "§9", "§a", "§b", "§c", "§d", "§e", "§f", "§k", "§l", "§m", "§n", "§o", "§r");
		$string = str_replace($check, "", $string);
		return $string;
	}

	public function creatProtectNumber(): int
	{
		$config = $this->subdata;
		if ($config->exists("protectNum")) {
			$num = $config->get("protectNum");
			$new = $num + 1;
		} else {
			$new = 0;
		}
		$config->set("protectNum", $new);
		$config->save();
		return $new;
	}

	public function getSubData()
	{
		return $this->subdata;
	}

	public function getVip(): Config
	{
		return $this->vip;
	}
}
