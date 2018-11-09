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
class Debug extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function debug(): bool
	{
		Exe::sendMessage(
			[
				"text" => "<pre>".htmlspecialchars(json_encode($this->data->in,
					JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
				), ENT_QUOTES, "UTF-8")."</pre>",
				"chat_id" => $this->data["chat_id"],
				"reply_to_message_id" => $this->data["msg_id"],
				"parse_mode" => "HTML"
			]
		);

		return true;
	}
}
