<?php

namespace Bot\Facebook\Responses;

use Bot\Facebook\Exe;
use Bot\Facebook\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Facebook\Responses
 * @version 4.0
 */
class Sh extends ResponseFoundation
{
	/**
	 * @return void
	 */
	public function shell(): bool
	{
		$st = new Exe(TOKENS["EsTehkuSegar"]);

		if (preg_match("/(?:^.sh\s)(.*)$/", $this->data["message"]["text"], $m)) {
			$exe = trim(shell_exec($m[1]." 2>&1"));
		} else {
			$exe = "~";
		}

		if ($exe === "") {
			$exe = "~";
		}

		$st->post("v2.6/me/messages", [
			"recipient" => [
				"id" => $this->data["sender"]["id"]
			],
			"message" => [
				"text" => "Ping OK!\n".round(time() - ($this->data["timestamp"]/1000), 5)." s"
			]
		]);

		return true;
	}
}
