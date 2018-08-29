<?php

namespace Bot\Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram
 * @version 4.0
 */
final class Response
{
	/**
	 * @var \Bot\Telegtram\Data
	 */
	private $data;

	/**
	 * @param \Bot\Telegtram\Data $data
	 */
	public function __construct(Data $data)
	{
		$this->data = $data;
	}

	/**
	 * @return void
	 */
	public function run(): void
	{
		if (preg_match("/\/me/Usi", $this->data["text"])) {
			Exe::sendMessage(
				[
					"text" => "There is no data stored for this user.",
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
		}
	}
}
