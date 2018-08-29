<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram\Responses
 * @version 4.0
 */
class Ping extends ResponseFoundation
{
	/**
	 * @return void
	 */
	public function ping(): bool
	{
		Exe::sendMessage(
			[
				"text" => "Ok",
				"chat_id" => $this->data["chat_id"],
				"reply_to_message_id" => $this->data["msg_id"]
			]
		);

		return true;
	}
}
