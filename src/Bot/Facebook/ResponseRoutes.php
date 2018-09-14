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
		/**
		 * Ping command
		 *
		 * Examples: ["/ping", "!ping", "~ping", ".ping"]
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
		 * Examples: ["/me", "!me", "~me", ".me"]
		 */
		$this->set(function($d) {
			return [
				(bool) preg_match("/(?:^|\s)(\!|\/|\~|\.)me(?:$|\s)/Usi", $d["text"]),
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

		/**
		 * Kulgram command
		 *
		 * Examples: ["/kulgram", "!kulgram"]
		 */
		$this->set(function($d) {
			return [
				(bool) preg_match("/^(\!|\/|\~|\.)kulgram/Usi", $d["text"]),
				[]
			];
		}, "Kulgram@handle");

		/**
		 * Translate command
		 * 
		 * Example: ["/tr en id How are you?", "!tr en id What time is it?"]
		 */
		$this->set(function($d) {
			return [
				(bool) preg_match("/^(\!|\/|\~|\.)?t(l|r)($|[\s\n])/Usi", $d["text"]),
				[]
			];
		}, "Translate@googleTranslate");

		$this->set(function($d) {
			return [
				(bool) preg_match("/^(\!|\/|\~|\.)?(tlr|trl)($|[\s\n])/Usi", $d["text"]),
				[]
			];
		}, "Translate@googleTranslatetoRepliedMessage");
	}
}
