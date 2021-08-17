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


use bboyyu51\pmdiscord\Sender;
use bboyyu51\pmdiscord\structure\Content;
use bboyyu51\pmdiscord\structure\Embed;
use bboyyu51\pmdiscord\structure\Embeds;
use pocketmine\scheduler\Task;
use pocketmine\utils\Utils;


class ServerStatusTask extends Task
{
    /**
     * @var main
     */
    private $server;

    /**
     * @var bool
     */
    private $bool;

    /**
     * ServerStatusTask constructor.
     * @param main $main
     * @param bool $bool
     */
    public function __construct(main $main)
    {
        $this->server = $main;
    }

    public function onRun(int $currentTick)
    {
        $server = $this->server;

        $webhook = $server->getWebHook();
        $webhook->setCustomName("Start");

        $var = $this->server->getDescription()->getVersion();

        $web = $this->server->getDescription()->getWebsite();

        $data = date("Y/m/d H:i:s");

        $p = $server->data->getAll();
        $p = count($p);

        $apivar = $this->server->getServer()->getApiVersion();

        $mcbe = $this->server->getServer()->getVersion();

        $max = $server->getServer()->getMaxPlayers();

        $sys = "Working normally";
        if (!$server->getServer()->getPluginManager()->getPlugin("Seichi_Alpha"))
        {
            $sys = "Not working";
        }
        if (!$server->getServer()->getPluginManager()->getPlugin("StackStrage_core"))
        {
            $sys = "Not working";
        }
        if (!$server->getServer()->getPluginManager()->getPlugin("ReefNetwork_core"))
        {
            $sys = "Not working";
        }

        $pls = $server->getServer()->getPluginManager()->getPlugins();
        $pls = count($pls);

        $ip = Utils::getIP();

        $port = $server->getServer()->getPort();

        $os = Utils::getOS();

        $uuid = Utils::getMachineUniqueId();

        $motd = $this->server->getServer()->getMotd();

        echo 'oooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo'."\n";
        echo 'oo                                                                                                oo'."\n";
        echo 'oo   RRRRRR                 fff NN   NN        tt                             kk                  oo '."\n";
        echo 'oo   RR   RR   eee    eee  ff   NNN  NN   eee  tt    ww      ww  oooo  rr rr  kk  kk              oo'."\n";
        echo 'oo   RRRRRR  ee   e ee   e ffff NN N NN ee   e tttt  ww      ww oo  oo rrr  r kkkkk               oo'."\n";
        echo 'oo   RR  RR  eeeee  eeeee  ff   NN  NNN eeeee  tt     ww ww ww  oo  oo rr     kk kk               oo'."\n";
        echo 'oo   RR   RR  eeeee  eeeee ff   NN   NN  eeeee  tttt   ww  ww    oooo  rr     kk  kk              oo'."\n";
        echo 'oo                                                                                                oo'."\n";
        echo 'oo                                                                                                oo'."\n";
        echo 'oo    ServerStatus                                                                                oo'."\n";
        echo 'oo                                                                                                oo'."\n";
        echo 'oo    Version                                     '.$this->onlength($var).'                  oo'."\n";
        echo 'oo    Web                                         '.$this->onlength($web).'                  oo'."\n";
        echo 'oo    ApiVersion                                  '.$this->onlength($apivar).'                  oo'."\n";
        echo 'oo    Access Version                              '.$this->onlength($mcbe).'                  oo'."\n";
        echo 'oo    Data                                        '.$this->onlength($data).'                  oo'."\n";
        echo 'oo    PlayerData                                  '.$this->onlength($p).'                  oo'."\n";
        echo 'oo    MaxPlayers                                  '.$this->onlength($max).'                  oo'."\n";
        echo 'oo    System                                      '.$this->onlength($sys).'                  oo'."\n";
        echo 'oo    Plugins                                     '.$this->onlength($pls).'                  oo'."\n";
        echo 'oo    Ip                                          '.$this->onlength($ip).'                  oo'."\n";
        echo 'oo    Port                                        '.$this->onlength($port).'                  oo'."\n";
        echo 'oo    Os                                          '.$this->onlength($os).'                  oo'."\n";
        echo 'oo    System Uuid                                 '.$this->onlength($uuid).'                  oo'."\n";
        echo 'oo    Motd                                        '.$this->onlength($motd).'                  oo'."\n";
        echo 'oo                                                                                                oo'."\n";
        echo 'oo                                @copyright 2019 Ree-jp                                          oo'."\n";
        echo 'oo                                                                                                oo'."\n";
        echo 'oooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo'."\n";

        $embed = new Embed();
        $embed->addField("Ip",$ip);
        $embed->addField("Port",$port);
        $embed->setAuthorName("Status");
        if ($sys != "Working normally")
        {
            $this->server->getLogger()->critical("System Not working");
            $this->server->getLogger()->warning("Block Server Participation  Reason:System Not working");
            $this->server->setOpen("System Not working");
            $embed->addField("System","System Not working");
            $content = new Content();
            $content->setText("error");
            $webhook->add($content);
        }else{
            $embed->addField("System","Working normally");
        }
        $embeds = new Embeds();
        $embeds->add($embed);
        $webhook->add($embeds);
        Sender::sendAsync($webhook);
    }

    private function onlength($data)
    {
        $string = (string) $data;
        $length = mb_strlen($string);
        if ($length <= 0)
        {
            $string = "not found";
            $length = mb_strlen($string);
        }
        if ($length > 30)
        {
            $string = "Could not show in status";
            $length = mb_strlen($string);
        }

        for (;$length < 30 ;$length++)
        {
            $string = $string." ";
        }
        return $string;
    }
}