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


		$this->set(function($d) {

			$s2 = substr($d["text"], 0, 2);
			$s3 = substr($d["text"], 0, 3);
			$s4 = substr($d["text"], 0, 4);
			$s5 = substr($d["text"], 0, 5);
			$s6 = substr($d["text"], 0, 6);
			$s7 = substr($d["text"], 0, 7);
			$s8 = substr($d["text"], 0, 8);
			$s9 = substr($d["text"], 0, 9);
			
			if (
				$s2 === "sh"		||
				$s3 === "!sh"		||
				$s3 === "/sh"		||
				$s3 === "~sh"		||
				$s3 === ".sh"		||

				$s5 === "<?php" 	||
				
				$s5 === "<?cpp" 	||
				$s5 === "<?c++" 	||
				$s5 === "<?g++" 	||

				$s5 === "<?gcc" 	||
				$s3 === "<?c" 		||

				$s4 === "<?js" 		||
				$s6 === "<?node"	||
				$s8 === "<?nodejs"	||

				$s4 === "<?pl" 		||
				$s6 === "<?perl" 	||

				$s4 === "<?rb" 		||
				$s6 === "<?ruby" 	||

				$s4 === "<?cr" 		||
				$s9 === "<?crystal" ||

				$s6 === "<?java"
			) {
				return [true, []];
			}
		}, "Virtualizor@virt");
	}
}
