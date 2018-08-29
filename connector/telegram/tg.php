<?php

if (isset($argv[1])) {
	require __DIR__."/../../bootstrap/init.php";
	require __DIR__."/../../config/telegram/main.php";

	$bot = new \Bot\Telegram\Bot(urldecode($argv[1]));
	$bot->run();
}
