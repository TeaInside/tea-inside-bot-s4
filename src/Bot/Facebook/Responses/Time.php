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
class Time extends ResponseFoundation
{
	/**
	 * @return void
	 */
	public function showTime(): bool
	{
		$st = new Exe(TOKENS["EsTehkuSegar"]);

		$st->post("v2.6/me/messages", [
			"recipient" => [
				"id" => $this->data["sender"]["id"]
			],
			"message" => [
				"text" => date("d F Y h:i:s A")
			]
		]);

		return true;
	}
}
