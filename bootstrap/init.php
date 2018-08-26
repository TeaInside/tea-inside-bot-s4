<?php

if (! defined("BOT_VERSION")) {

	define("BOT_VERSION", "4.0 Beta");

	require __DIR__."/../config/init.php";

	if (! defined("BASEPATH")) {
		print("BASEPATH is not defined\n");
		exit(1);
	}
	
	if (file_exists($f = BASEPATH."/vendor/autoload.php")) {
		require $f;
	}

	/**
	 * @param string $class
	 * @return void
	 */
	function iceteaInternalAutoloader(string $class): void
	{
		$class = str_replace("\\", "/", $class);

		if (file_exists($f = BASEPATH."/src/".$class.".php")) {
			require $f;
		}
	}

	spl_autoload_register("iceteaInternalAutoloader");

	require BASEPATH."/src/helpers.php";
}
