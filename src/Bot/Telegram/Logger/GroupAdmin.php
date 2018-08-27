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
class GroupAdmin implements LoggerInterface
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
	 * @var bool
	 */
	public $run = true;

	/**
	 * @var bool
	 */
	public $reset = true;

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
		var_dump($this->reset);

		if ($this->reset) {
			$this->pdo->prepare(
				"DELETE FROM `group_admin` WHERE `group_id`=:group_id;"
			)->execute([":group_id" => $this->data["group_id"]]);
			var_dump("deleted");
		}

		if ($this->run) {
			$a = Exe::getChatAdministrators(
				[
					"chat_id" => $this->data["group_id"]
				]
			);
			$a = json_decode($a["out"], true);
			$query = "INSERT INTO `group_admin` (`group_id`, `user_id`, `role`, `created_at`) VALUES ";
			$now = date("Y-m-d H:i:s");
			if (isset($a["result"])) {
				foreach ($a["result"] as $key => $admin) {
					$this->adminData[] = $admin;
					$query .= "(:group_id, :user_id_{$key}, :role_{$key}, :created_at_{$key}),";
					$data[":group_id"] = $this->data["group_id"];
					$data[":user_id_{$key}"] = $admin["user"]["id"];
					$data[":role_{$key}"] = $admin["status"];
					$data[":created_at_{$key}"] = $now;
					$admin = $admin["user"];
					if (! isset($admin["last_name"])) {
						$admin["last_name"] = null;
					}
					if (! isset($admin["username"])) {
						$admin["username"] = null;
					}
					$st = $this->pdo->prepare("SELECT `first_name`,`last_name`,`username`,`photo` FROM `users` WHERE `id`=:id LIMIT 1;");
					$st->execute([":id" => $admin["id"]]);
					if ($u = $st->fetch(PDO::FETCH_ASSOC)) {
						$this->updateUser($u, $admin);
					} else {
						$this->addNewUser($admin);
					}
				}
				$st = $this->pdo->prepare(trim($query, ","));
				$st->execute($data);
			}
		}
	}

	/**
	 * @param array $u
	 * @param array $admin
	 * @return void
	 */
	private function updateUser(array $u, array $admin): void
	{
		$query	= "UPDATE `users` SET ";
		$data	= [];
		$now	= date("Y-m-d H:i:s");
		if ($u["first_name"] !== $admin["first_name"]) {
			$query .= "`first_name`=:first_name, ";
			$data[":first_name"] = $admin["first_name"];
		}
		if ($u["last_name"] !== $admin["last_name"]) {
			$query .= "`last_name`=:last_name, ";
			$data[":last_name"] = $admin["last_name"];
		}
		if ($u["username"] !== $admin["username"]) {
			$query .= "`username`=:username, ";
			$data[":username"] = $admin["username"];
		}

		if (! empty($data)) {
			$this->addUserHistory($admin);
			$query .= "`updated_at`=:updated_at ";
			$data[":updated_at"] = $this->data["_now"];
			
			$query .= "WHERE `id`=:id LIMIT 1;";
			$data[":id"] = $admin["user_id"];
			$st = $this->pdo->prepare($query);
			$st->execute($data);
			unset($st);
		}
	}

	/**
	 * @param string $admin
	 * @return void
	 */
	private function addNewUser(array $admin): void
	{
		$st = $this->pdo->prepare(
			"INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `photo`, `private_message_count`, `group_message_count`, `created_at`, `updated_at`) VALUES (:id, :first_name, :last_name, :username, :photo, :private_message_count, :group_message_count, :created_at, :updated_at);"
		);
		$st->execute(
			[
				":id" => $admin["id"],
				":first_name" => $admin["first_name"],
				":last_name" => $admin["last_name"],
				":username" => $admin["username"],
				":photo" => null,
				":private_message_count" => 0,
				":group_message_count" => 0,
				":created_at" => $this->data["_now"],
				":updated_at" => null
			]
		);

		$this->addUserHistory($admin);
	}

	/**
	 * @param array $admin
	 * @return void
	 */
	private function addUserHistory(array $admin): void
	{
		$st = $this->pdo->prepare("INSERT INTO `user_history` (`user_id`, `first_name`, `last_name`, `username`, `photo`, `created_at`) VALUES (:user_id, :first_name, :last_name, :username, :photo, :created_at);");
		$st->execute(
			[
				":user_id" => $admin["id"],
				":first_name" => $admin["first_name"],
				":last_name" => $admin["last_name"],
				":username" => $admin["username"],
				":photo" => null,
				":created_at" => $this->data["_now"]
			]
		);
	}
}
