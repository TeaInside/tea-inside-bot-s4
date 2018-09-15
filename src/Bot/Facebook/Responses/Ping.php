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
class Ping extends ResponseFoundation
{
	/**
	 * @return void
	 */
	public function ping(): bool
	{
		$st = new Exe(TOKENS["EsTehkuSegar"]);
		var_dump($this->data["timestamp"]);
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
