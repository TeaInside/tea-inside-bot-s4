<?php

$input = urlencode(file_get_contents("php://input"));

shell_exec(
	"nohup ".PHP_BINARY." ".__DIR__."/logger.php \"".$input."\" >> ".__DIR__."/../../logs/telegram/logger.log 2>&1 &"
);

shell_exec(
	"nohup ".PHP_BINARY." ".__DIR__."/tg.php \"".$input."\" >> ".__DIR__."/../../logs/telegram/bg.log 2>&1 &"
);
