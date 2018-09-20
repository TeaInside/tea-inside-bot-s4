<?php

if (file_exists("installed.state")) {
	printf("You have already been installed the tea-inside-bot-s4!\n");
	exit(0);
}

function configCpy()
{
	$listConfig = [
		__DIR__."/config/line/main.php",
		__DIR__."/config/telegram/main.php",
		__DIR__."/config/facebook/main.php"
	];

	foreach ($listConfig as $config) {
		printf("Copying %s.example to %s...", $config, $config);
		if (copy($config.".example", $config)) {
			printf("OK\n");
		} else {
			printf("An error occured when copying %s.example to %s", $config, $config);
		}
	}
}

