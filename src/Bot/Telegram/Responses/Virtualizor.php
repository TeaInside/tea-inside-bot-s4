<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Isolator\Virtualizor as BaseVirtualizor;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram\Responses
 * @version 4.0
 */
class Virtualizor extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function virt(): bool
	{
		$st = new BaseVirtualizor();

		$s3 = substr($this->data["text"], 0, 3);
		$s4 = substr($this->data["text"], 0, 4);
		$s5 = substr($this->data["text"], 0, 5);
		$s6 = substr($this->data["text"], 0, 6);
		$s7 = substr($this->data["text"], 0, 7);
		$s8 = substr($this->data["text"], 0, 8);
		$s9 = substr($this->data["text"], 0, 9);

		$st->setId($this->data["user_id"]);

		if ($s5 === "<?php") {
			$st->run(
				$this->data["text"], 
				"php"
			);
		}

		Exe::sendMessage(
			[
				"text" => $st->getResult(),
				"chat_id" => $this->data["chat_id"],
				"reply_to_message_id" => $this->data["msg_id"],
			]
		);

		return true;
	}
}
