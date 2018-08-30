<?php

namespace Bot\Telegram\Responses;

use Singleton;
use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;
use GoogleTranslate\GoogleTranslate;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram\Responses
 * @version 4.0
 */
class Translate extends ResponseFoundation
{
	/**
	 * @return void
	 */
	public function googleTranslate(): bool
	{
		if (preg_match("/^(?:\!|\/|\~|\.)(?:tr[\s\n]+)([a-z]{2}|auto)(?:[\s\n]+)([a-z]{2})(?:[\s\n]+)?(.*)?$/Usi", $this->data["text"], $m)) {
			if (isset($m[3])) {
				$st = new GoogleTranslate(trim($m[3]), $m[1], $m[2]);
				$st = trim($st->exec());
				$st = $st === "" ? "~" : $st;
				Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => $st,
						"reply_to_message_id" => $this->data["msg_id"]
					]
				);
				return true;
			} elseif (isset($this->data["reply_to"]["message_id"], $this->data["reply_to"]["text"])) {
				$st = new GoogleTranslate($this->data["reply_to"]["text"], $m[1], $m[2]);
				$st = trim($st->exec());
				$st = $st === "" ? "~" : $st;
				Exe::sendMessage(
					[
						"chat_id" => $this->data["chat_id"],
						"text" => $st,
						"reply_to_message_id" => $this->data["reply_to"]["message_id"]
					]
				);
				return true;
			}
		}

		$singleton = Singleton::getInstance();
		$lang = $singleton->get("lang");
		unset($singleton);
		Exe::sendMessage(
			[
				"chat_id" => $this->data["chat_id"],
				"text" => $lang->get("translate.usage.basic"),
				"reply_to_message_id" => $this->data["msg_id"]
			]
		);

		return true;
	}
}
