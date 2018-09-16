<?php

namespace Bot\Facebook\Responses;

use Error;
use Bot\Facebook\Exe;
use Bot\Facebook\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Facebook\Responses
 * @version 4.0
 */
class Calculator extends ResponseFoundation
{
	/**
	 * @return void
	 */
	public function calc(): bool
	{
		$st = new Exe(TOKENS["EsTehkuSegar"]);

		$expr = null;

		if (preg_match("/^(?:.calc[\s\n])(.*)$/Usi", $this->data["message"]["text"], $m)) {
			$expr = trim($m[1]);
		}

		if ($expr === null) {
			$r = "";
		} else {
			try {
				eval("\$r = {$expr};");	
			} catch (Error $e) {
				$r = "Invalid math expression!";
			}
		}		
		
		$st->post("v2.6/me/messages", [
			"recipient" => [
				"id" => $this->data["sender"]["id"]
			],
			"message" => [
				"text" => json_encode($r)
			]
		]);

		return true;
	}
}
