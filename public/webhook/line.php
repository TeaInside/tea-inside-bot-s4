<?php

if (isset($_GET["key"])) {
	require __DIR__."/../../config/line/webhook_key.php";
	if ($_GET["key"] !== WEBHOOK_KEY) {
		http_response_code(403);
		header("Content-Type: text/plain");
		exit("Forbidden!");
	}
} else {
	http_response_code(403);
	header("Content-Type: text/plain");
	exit("Forbidden!");
}
require __DIR__."/../../connector/line/first.php";
