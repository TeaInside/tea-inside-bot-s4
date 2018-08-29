<?php

namespace Bot\Telegram\Logger;

use DB;
use PDO;
use Bot\Telegram\Exe;
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
		if (in_array($this->data["msg_type"], ["text", "photo"])) {			
			if ($this->data["chat_type"] === "group") {
				$this->groupLogger();
			} elseif ($this->data["chat_type"] === "private") {
				$this->privateLogger();
			}
		}
	}

	/**
	 * @return void
	 */
	private function groupLogger(): void
	{
		$file_id = null;

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

		if (in_array($this->data["msg_type"], ["photo"])) {
			$file_id = $this->trackFile();
		}

		$st = $this->pdo->prepare(
			"INSERT INTO `group_messages_data` (`group_message_id`, `text`, `file_id`, `type`)
			VALUES (:group_message_id, :text, :file_id, :type);"
		);

		$st->execute(
			[
				":group_message_id" => $group_message_id,
				":text" => $this->data["text"],
				":file_id" => $file_id,
				":type" => $this->data["msg_type"]
			]
		);
	}

	/**
	 * @return void
	 */
	private function privateLogger(): void
	{
		$file_id = null;

		$st = $this->pdo->prepare(
			"INSERT INTO `private_messages` (`user_id`, `telegram_msg_id`, `reply_to_msg_id`, `created_at`, `updated_at`)
			VALUES (:user_id, :telegram_msg_id, :reply_to_msg_id, :created_at, :updated_at);"
		);

		$st->execute(
			[
				":user_id" => $this->data["user_id"],
				":telegram_msg_id" => $this->data["msg_id"],
				":reply_to_msg_id" => (
					isset($this->data->in["message"]["reply_to_message"]["message_id"]) ?
						$this->data->in["message"]["reply_to_message"]["message_id"] :
							null
				),
				"created_at" => $this->data["_now"],
				"updated_at" => null
			]
		);

		$private_message_id = $this->pdo->lastInsertId();

		if (in_array($this->data["msg_type"], ["photo"])) {
			$file_id = $this->trackFile();
		}

		$st = $this->pdo->prepare(
			"INSERT INTO `private_messages_data` (`private_message_id`, `text`, `file_id`, `type`)
			VALUES (:private_message_id, :text, :file_id, :type);"
		);

		$st->execute(
			[
				":private_message_id" => $private_message_id,
				":text" => $this->data["text"],
				":file_id" => $file_id,
				":type" => $this->data["msg_type"]
			]
		);
	}

	/**
	 * @return int
	 */
	private function trackFile(): int
	{
		switch ($this->data["msg_type"]) {
			case 'photo':
					$p = $this->data['photo'][count($this->data['photo']) - 1];
					$telegram_file_id = $p['file_id'];
					$st = $this->pdo->prepare(
						"SELECT `id` FROM `files` WHERE `telegram_file_id`=:telegram_file_id LIMIT 1;"
					);
					$st->execute([":telegram_file_id" => $telegram_file_id]);
					if ($st = $st->fetch(PDO::FETCH_NUM)) {
						$this->pdo->prepare(
							"UPDATE `files` SET `hit_count`=`hit_count`+1, `updated_at`=:updated_at WHERE `telegram_file_id`=:telegram_file_id LIMIT 1;"
						)->execute(
							[
								":updated_at" => $this->data["_now"],
								":telegram_file_id" => $telegram_file_id
							]
						);
						return $st[0];
					}
					unset($p["file_id"]);
					$a = json_decode($out = Exe::getFile(["file_id" => $telegram_file_id])["out"], true);

					if (isset($a["result"]["file_path"])) {
						unset($out);
				        $ch = curl_init("https://api.telegram.org/file/bot".TOKEN."/".$a['result']['file_path']);
				        curl_setopt_array($ch,
				        	[
				        		CURLOPT_RETURNTRANSFER => true,
				        		CURLOPT_SSL_VERIFYPEER => false,
				        		CURLOPT_SSL_VERIFYHOST => false
				        	]
				        );
				        $binary = curl_exec($ch);
				        curl_close($ch);

				        is_dir(STORAGE_PATH) or mkdir(STORAGE_PATH);
				        is_dir(STORAGE_PATH."/files") or mkdir(STORAGE_PATH."/files");

				        $filename = ($sha1 = sha1($binary))."_".($md5 = md5($binary)).".jpg";
				        $handle = fopen(STORAGE_PATH."/files/".$filename, "w");
				        flock($handle, LOCK_EX);
				        fwrite($handle, $binary);
				        fclose($handle);

				        unset($binary, $handle, $filename, $ch, $p, $a, $st);

				        $this->pdo->prepare(
				        	"INSERT INTO `files` (`type`, `telegram_file_id`, `md5_checksum`, `sha1_checksum`, `absolute_hash`, `hit_count`, `description`, `created_at`, `updated_at`)
				        	VALUES (:type, :telegram_file_id, :md5_checksum, :sha1_checksum, :absolute_hash, :hit_count, :description, :created_at, :updated_at);"
				        )->execute(
				        	[
				        		":type" => "photo",
				        		":telegram_file_id" => $telegram_file_id,
				        		":md5_checksum"  => $md5,
				        		":sha1_checksum" => $sha1,
				        		":absolute_hash" => ($sha1."_".$md5),
				        		":hit_count" => 1,
				        		":description" => null,
				        		":created_at" => $this->data["_now"],
				        		":updated_at" => null
				        	]
				        );

				        return $this->pdo->lastInsertId();
				    } else {
				    	print "Could not get the file: ".$out."\n\n";
				    }
				break;
			
			default:
				break;
		}


	}
}
