<?php

namespace Ree\plugin\socket;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class ReceiveTask extends AsyncTask
{
	private $socket;
	public function onRun()
	{
		$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		$socket = $this->socket;
		if ($socket)
		{
			socket_bind($socket ,'172.0.0.1' ,30002);
			socket_listen($socket);
			socket_accept($socket);
			$buf = socket_read($socket ,2024);
			$this->setResult($buf);
			$this->close();
		}else{
			$this->setResult(socket_strerror(socket_last_error()));
			sleep(10);
		}
	}

	public function close()
	{
		socket_close($this->socket);
	}

	public function onCompletion(Server $server)
	{
		$message = $this->getResult();
		$message = '[§1Discord§r] '.$message;
		$server->broadcastMessage($message);
		$task = new ReceiveTask();
		main::$receiveTask = $task;
		$server->getAsyncPool()->submitTask($task);
	}
}