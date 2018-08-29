<?php

namespace Bot\Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram
 * @version 4.0
 */
trait ResponseRoutes
{
	/**
	 * @return void
	 */
	private function buildRoutes(): void
	{
		/**
		 * Ping command
		 *
		 * Example: ["/ping", "!ping", "~ping", ".ping"]
		 */
		$this->set(function($d) {
			return [
				(bool) preg_match("/^(\!|\/|\~|\.)ping$/", $d["text"]),
				[]
			];
		}, "Ping@ping");

		/**
		 * Me command
		 *
		 * Example: ["/me", "!me", "~me", ".me"]
		 */
		$this->set(function() {
			return [
				(bool) preg_match("/(?:^|\s)(\!|\/|\~|\.)me(?:$|\s)/Usi", $this->data["text"]),
				[]
			];
		}, function () {
			Exe::sendMessage(
				[
					"text" => "There is no data stored for this user.",
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
		});
	}
}
