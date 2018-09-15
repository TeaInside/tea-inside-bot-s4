<?php

namespace Bot\Facebook;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Facebook
 * @version 4.0
 */
trait ResponseRoutes
{
	/**
	 * @return void
	 */
	private function buildRoutes(): void
	{
		if (isset($this->data["message"]["text"])) {
			$txt = $this->data["message"]["text"];
		} else {
			$txt = null;
		}


		$this->set(function () use ($txt) {
			var_dump("ping");
			return [
				(bool) preg_match("/^(\!|\/|\~|\.)ping$/", $d["text"]),
				[]
			];
		}, "Ping@ping");
	}
}
