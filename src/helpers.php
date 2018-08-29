<?php

if (! function_exists("ee")) {
	function ee(string $str): string
	{
		return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
	}
}
