<?php

namespace Bot\Facebook\Responses;

use Bot\Facebook\Exe;
use Bot\Facebook\Lang;
use Bot\Facebook\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Facebook\Responses
 * @version 4.0
 */
class Help extends ResponseFoundation
{
	/**
	 * @return void
	 */
	public function menu(): bool
	{
		$st = new Exe(TOKENS["EsTehkuSegar"]);
		
		$st->post("v2.6/me/messages", [
			"recipient" => [
				"id" => $this->data["sender"]["id"]
			],
			"message" => [
				"text" => Lang::get("help.general.menu")
			]
		]);

		return true;
	}
}
