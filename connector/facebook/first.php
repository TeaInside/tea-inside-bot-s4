<?php

header("Content-Type: text/plain");

if (isset($_REQUEST["hub_mode"], $_REQUEST["hub_challenge"], $_REQUEST["hub_verify_token"])) {
	
	require __DIR__."/../../config/facebook/hub_verify_token.php";
	
	switch ($_REQUEST["hub_mode"]) {
		case 'subscribe':
			if (! defined("HUB_VERIFY_TOKEN")) {
				exit("HUB_VERIFY_TOKEN is not defined yet!\n");
			}
			if ($_REQUEST["hub_verify_token"] === HUB_VERIFY_TOKEN) {
				print $_REQUEST["hub_challenge"];
			}		
			break;
		
		default:
			break;
	}
	exit;
}


$input = urlencode(file_get_contents("php://input"));

shell_exec(
	"nohup /usr/bin/php7.2 ".__DIR__."/fb.php \"".$input."\" >> ".__DIR__."/../../logs/facebook/bg.log 2>&1 &"
);

// shell_exec(
// 	"nohup /usr/bin/php7.2 ".__DIR__."/logger.php \"".$input."\" >> ".__DIR__."/../../logs/telegram/logger.log 2>&1 &"
// );

file_put_contents(__DIR__."/../../logs/facebook/first.log", 
	json_encode(json_decode($input), 128)
);
