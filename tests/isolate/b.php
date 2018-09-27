<?php

require __DIR__."/../../bootstrap/init.php";
require __DIR__."/../../config/telegram/main.php";

use Isolator\Virtualizor;

$st = new Virtualizor;
$st->setId(1);
$st->run(
	"<?php print shell_exec('env');",
	"php"
);
$r = $st->getResult();

var_dump($r);