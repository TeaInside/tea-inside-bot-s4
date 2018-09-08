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
class Group implements LoggerInterface
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
		$st = $this->pdo->prepare(
			"SELECT `name`,`username`,`link`,`photo`,`msg_count` FROM `groups` WHERE `id` = :id LIMIT 1;"
		);
		$st->execute([":id" => $this->data["group_id"]]);

		if ($st = $st->fetch(PDO::FETCH_ASSOC)) {
			$this->updateGroup($st);
		} else {
			$this->addNewGroup();
		}
	}

	/**
	 * @param array $g
	 * @return void
	 */
	private function updateGroup(array $g): void
	{
		$query = "UPDATE `groups` SET ";
		$data = [];

		if ($this->data["group_name"] !== $g["name"]) {
			$query .= "`name`=:name, ";
			$data[":name"] = $this->data["group_name"];
		}

		if ($this->data["group_username"] !== $g["username"]) {
			$query .= "`username`=:username, ";
			$data[":username"] = $this->data["group_username"];
		}

		if (! empty($data)) {
			$this->addGroupHistory();
		}

		$query .= "`msg_count`=`msg_count`+1, ";
		$query .= "`updated_at`=:updated_at ";
		$data[":updated_at"] = $this->data["_now"];

		if ($g["msg_count"] % 10 === 0) {
			$st = new GroupAdmin($this->data);
			$st->run = true;
			$st->reset = true;
			$st->run();
		}

		$query .= "WHERE `id`=:id LIMIT 1;";
		$data[":id"] = $this->data["group_id"];
		$st = $this->pdo->prepare($query);
		$st->execute($data);
	}

	/**
	 * @return void
	 */
	private function addNewGroup(): void
	{
		$now = date("Y-m-d H:i:s");
		$st = $this->pdo->prepare("INSERT INTO `groups` (`id`, `name`, `username`, `link`, `photo`, `msg_count`, `created_at`, `updated_at`) VALUES (:id, :name, :username, :link, :photo, :msg_count, :created_at, :updated_at);");
		$st->execute(
			[
				":id" => $this->data["group_id"],
				":name" => $this->data["group_name"],
				":username" => $this->data["group_username"],
				":link" => null,
				":photo" => null,
				":msg_count" => 1,
				":created_at" => $now,
				":updated_at" => null
			]
		);
		$this->pdo->prepare("INSERT INTO `group_settings` (`group_id`) VALUES (:group_id);")->execute(
			[
				":group_id" => $this->data["group_id"]
			]
		);
		$this->addGroupHistory();
		$st = new GroupAdmin($this->data);
		$st->run = true;
		$st->reset = false;
		$st->run();
	}

	/**
	 * @return void
	 */
	private function addGroupHistory(): void
	{
		$st = $this->pdo->prepare("INSERT INTO `group_history` (`group_id`, `name`, `username`, `link`, `photo`, `created_at`) VALUES (:group_id, :name, :username, :link, :photo, :created_at);");
		$st->execute(
			[
				":group_id" => $this->data["group_id"],
				":name" => $this->data["group_name"],
				":username" => $this->data["group_username"],
				":link" => null,
				":photo" => null,
				":created_at" => $this->data["_now"]
			]
		);
	}
}
