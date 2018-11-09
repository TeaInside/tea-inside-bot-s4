<?php

$input = escapeshellarg(file_get_contents("php://input"));

shell_exec(
	"nohup /usr/bin/php7.2 ".__DIR__."/bot.php {$input} >> ".__DIR__."/../../logs/telegram/bg.log 2>&1 &"
);

shell_exec(
	"nohup /usr/bin/php7.2 ".__DIR__."/logger.php {$input} >> ".__DIR__."/../../logs/telegram/logger.log 2>&1 &"
);
