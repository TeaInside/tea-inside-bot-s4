<?php

namespace Bot\Telegram\Logger;

use DB;
use PDO;
use Bot\Telegram\Data;
use Bot\Telegram\Contracts\LoggerInterface;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram
 * @version 4.0
 */
class Message implements LoggerInterface
{
	/**
	 * @var \Bot\Telegram\Data
	 */
	private $data;

	/**
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * @param \Bot\Telegram\Data $data
	 *
	 * Constructor.
	 */
	public function __construct(Data $data)
	{
		$this->data = $data;
		$this->pdo  = DB::pdo();		
	}

	/**
	 * @return void
	 */
	public function run(): void
	{
		if (in_array($this->data["msg_type"], ["text"])) {
			if ($this->data["chat_type"] === "group") {
				$this->groupLogger();
			}
		}
	}

	/**
	 * @return void
	 */
	private function groupLogger(): void
	{
		$st = $this->pdo->prepare(
			"INSERT INTO `group_messages` (`group_id`, `user_id`, `telegram_msg_id`, `reply_to_msg_id`, `created_at`, `updated_at`)
			VALUES (:group_id, :user_id, :telegram_msg_id, :reply_to_msg_id, :created_at, :updated_at);"
		);

		$st->execute(
			[
				":group_id" => $this->data["group_id"],
				":user_id" => $this->data["user_id"],
				":telegram_msg_id" => $this->data["msg_id"],
				":reply_to_msg_id" => (
					isset($this->data->in["message"]["reply_to_message"]["message_id"]) ?
						$this->data->in["message"]["reply_to_message"]["message_id"] :
							null
				),
				":created_at" => $this->data["_now"],
				":updated_at" => null
			]
		);

		$group_message_id = $this->pdo->lastInsertId();

		$st = $this->pdo->prepare(
			"INSERT INTO `group_messages_data` (`group_message_id`, `text`, `file_id`, `type`)
			VALUES (:group_message_id, :text, :file_id, :type);"
		);

		$st->execute(
			[
				":group_message_id" => $group_message_id,
				":text" => $this->data["text"],
				":file_id" => null,
				":type" => $this->data["msg_type"]
			]
		);
	}
}
