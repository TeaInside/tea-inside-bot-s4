<?php

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