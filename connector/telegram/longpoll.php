<?php

require __DIR__."/../../bootstrap/init.php";
require __DIR__."/../../config/telegram/main.php";


$updateIdCacheFile = STORAGE_PATH."/cache/last_update_id";

while (true) {

	$lastUpdateId = file_exists($updateIdCacheFile) ? (int)file_get_contents($updateIdCacheFile) : 0;

	print ddd()."Getting new updates...";
	$updates = \Bot\Telegram\Exe::getUpdates([]);
	$updates = json_decode($updates["out"], true);
	$countResult = 0;
	if (is_array($updates["result"])) {
		print "OK\n";
		$countResult = count($updates["result"]);
		$exe = false;
		foreach ($updates["result"] as $v) {
			if ($v["update_id"] > $lastUpdateId) {
				print ddd()."Processing update id \"{$v['update_id']}\"...";
				$bot = new \Bot\Telegram\Bot(json_encode($v));
				$bot->run();
				print "Done\n";
				$exe = true;
				$lastUpdateId = $v["update_id"];
			}
		}

		if (!$exe) {
			print ddd()."There is no new update from telegram.\n";
		}
	}

	file_put_contents($updateIdCacheFile, $lastUpdateId);

	if ($countResult > 30) {
		// clean update
		print ddd()."Cleaning updates...";
		$a = \Bot\Telegram\Exe::{"getUpdates?offset=-1"}([]);
		print "OK\n";
	}
}

function ddd()
{
	return date("[d F Y H:i:s] ");
}