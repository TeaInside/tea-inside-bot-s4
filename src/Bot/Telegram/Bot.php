<?php

namespace Bot\Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram
 * @version 4.0
 */
final class Bot
{
	/**
	 * 
	 *
	 */
	public function __construct(string $json)
	{
		$this->data = new Data($json);
		print Exe::sendMessage(
			[
				"text" => "test",
				"chat_id" => $this->data["chat_id"]
			]
		)["out"];
	}
}
