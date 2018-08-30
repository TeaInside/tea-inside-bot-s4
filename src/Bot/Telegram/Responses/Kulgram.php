<?php

namespace Bot\Telegram\Responses;

use DB;
use PDO;
use Mpdf\Mpdf;
use Exception;
use Singleton;
use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram\Responses
 * @version 4.0
 */
class Kulgram extends ResponseFoundation
{
	/**
	 * @var \Bot\Telegram\Lang
	 */
	private $lang;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var array
	 */
	private $info = [];

	/**
	 * @var resource
	 */
	private $handle;

	/**
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * @return bool
	 */
	public function handle(): bool
	{
		$singleton = Singleton::getInstance();
		$this->lang = $singleton->get("lang");
		unset($singleton);

		if ($this->data["chat_type"] !== "group") {
			Exe::sendMessage(
				[
					"text" => $this->lang->get("kulgram.error.group_only"),
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
			return true;
		}

		$this->data["text"] = str_replace(chr(226).chr(128).chr(148), "--", $this->data["text"]);
		if (preg_match("/^(?:\\/|\\!|\\~)(?:kulgram)(?:[\\s\\n]{1,})?([^\\s\\n]+)?(?:[\\s\\n]{1,})?(.*)?$/", $this->data["text"], $m)) {

			$_argv = [];

			if (isset($m[2]) && preg_match_all("/\-{2}([^\\s\\n]+)(?:\\s+|\\n+|\=)((?:\\\"|\\')(.+)(?:[^(\\\\\")]\\\"|\\')|[^\\s\\n]+)(?:[\\s\\n]|$)/Usi", $m[2], $n)) {
				foreach ($n[2] as $k => &$v) {
					if (! empty($n[3][$k])) {
						$v = substr($v, 1, -1);
					}
					do {
						$v = str_replace("\\\"", "\"", $v, $nn);
					} while ($nn);
					do {
						$v = str_replace("\\\\", "\\", $v, $nn);
					} while ($nn);
					$_argv[$n[1][$k]] = ($v = trim($v));
				}
			}

			switch ($m[1]) {
				case 'cancel':
						return $this->cancel();
				case 'init':

					/**
					 * Error no title and no author.
					 */
					if ((!isset($_argv["title"])) && (!isset($_argv["author"]))) {
						Exe::sendMessage(
							[
								"chat_id" => $this->data["chat_id"],
								"text" => (
									$this->lang->get("kulgram.error.init_no_title_no_author")
									."\n\n"
									.$this->lang->get("kulgram.usage.init")
									."\n\n"
									.$this->lang->get("kulgram.usage.footer")
								),
								"reply_to_message_id" => $this->data["msg_id"],
								"parse_mode" => "HTML"
							]
						);
						return true;
					}

					/**
					 * Error no title.
					 */
					if (! isset($_argv["title"])) {
						Exe::sendMessage(
							[
								"chat_id" => $this->data["chat_id"],
								"text" => (
									$this->lang->get("kulgram.error.init_no_title")
									."\n\n"
									.$this->lang->get("kulgram.usage.init")
									."\n\n"
									.$this->lang->get("kulgram.usage.footer")
								),
								"reply_to_message_id" => $this->data["msg_id"],
								"parse_mode" => "HTML"
							]
						);
						return true;
					}

					/**
					 * Error no author.
					 */
					if (! isset($_argv["author"])) {
						Exe::sendMessage(
							[
								"chat_id" => $this->data["chat_id"],
								"text" => (
									$this->lang->get("kulgram.error.init_no_author")
									."\n\n"
									.$this->lang->get("kulgram.usage.init")
									."\n\n"
									.$this->lang->get("kulgram.usage.footer")
								),
								"reply_to_message_id" => $this->data["msg_id"],
								"parse_mode" => "HTML"
							]
						);
						return true;
					}

					return $this->init($_argv["title"], $_argv["author"]);
				case "start":
					return $this->start();
				case "stop":
					return $this->stop();
				default:
						Exe::sendMessage(
							[
								"chat_id" => $this->data["chat_id"],
								"text" => (
									$this->lang->get("kulgram.usage.basic")
									."\n\n"
									.$this->lang->get("kulgram.usage.footer")
								),
								"reply_to_message_id" => $this->data["msg_id"],
								"parse_mode" => "HTML"
							]
						);
					return true;
					break;
			}

		}
	}

	/**
	 * @throws \Exception
	 * @return bool
	 */
	private function loadData(): bool
	{
		$this->path = STORAGE_PATH."/kulgram/".str_replace("-", "_", $this->data["chat_id"]);
		is_dir(STORAGE_PATH."/kulgram") or mkdir(STORAGE_PATH."/kulgram");
		is_dir($this->path) or mkdir($this->path);

		if (! is_dir($this->path)) {
			throw new Exception("Cannot create directory {$this->path}");
		}

		if (file_exists($this->path."/info.json")) {
			$this->handle = fopen($this->path."/info.json", "r");
			flock($this->handle, LOCK_EX);
			$this->info = json_decode(file_get_contents($this->path."/info.json"), true);
			if (! isset($this->info["status"], $this->info["chat_id"], $this->info["count"])) {
				$this->info = [
					"status" => "sleep",
					"chat_id" => $this->data["chat_id"],
					"count" => 0
				];
			}
		} else {
			$this->handle = fopen($this->path."/info.json", "w");
			flock($this->handle, LOCK_EX);
			$this->info = [
				"status" => "sleep",
				"chat_id" => $this->data["chat_id"],
				"count" => 0
			];
		}

		if (
			(!in_array($this->data["user_id"], SUDOERS)) &&
			(!$this->isAdmin())
		) {
			Exe::sendMessage(
				[
					"text" => $this->lang->get("kulgram.error.access_denied"),
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	private function isAdmin(): bool
	{
		$this->pdo = DB::pdo();
		$st = $this->pdo->prepare(
			"SELECT `user_id` FROM `group_admin` WHERE `user_id`=:user_id AND `group_id`=:group_id LIMIT 1;"
		);
		$st->execute(
			[
				":user_id" => $this->data["user_id"],
				":group_id" => $this->data["chat_id"]
			]
		);
		if ($st->fetch(PDO::FETCH_ASSOC)) {
			return true;
		}

		unset($st);

		$a = Exe::getChatAdministrators(["chat_id" => $this->data["group_id"]]);
		$a = json_decode($a["out"], true);
		if (isset($a["result"])) {
			foreach ($a["result"] as $key => $admin) {
				if ($admin["id"] == $this->data["user_id"]) {
					return true;
				}
			}
		}

		return false;
	}
	
	/**
	 * @return bool
	 */
	private function cancel(): bool
	{
		if ($this->loadData()) {
			return true;
		}
		
		if ($this->info["status"] === "idle") {
			$this->info["status"] = "sleep";
			$this->info["count"]--;
			unset($this->info["session"]);
			Exe::sendMessage(
				[
					"text" => $this->lang->get("kulgram.run.cancel"),
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
		} elseif ($this->info["status"] === "sleep") {
			Exe::sendMessage(
				[
					"text" => $this->lang->get("kulgram.error.cancel_no_session"),
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
		} elseif ($this->info["status"] === "recording") {
			Exe::sendMessage(
				[					
					"text" => $this->lang->get("kulgram.error.cancel_when_recording"),
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"],
					"parse_mode" => "HTML"
				]
			);
		}
		
		return true;
	}

	/**
	 * @return bool
	 */
	private function start(): bool
	{
		if ($this->loadData()) {
			return true;
		}

		if ($this->info["status"] === "idle") {
			$this->info["status"] = "recording";
			$this->info["session"]["start_point"] = $this->data["msg_id"];
			$this->info["session"]["start_date"] = $this->data["_now"];
			Exe::sendMessage(
				[
					"text" => $this->lang->get("kulgram.run.start"),
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
		} elseif ($this->info["status"] === "sleep") {
			Exe::sendMessage(
				[
					"text" => $this->lang->get("kulgram.error.start_no_session"),
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"],
					"parse_mode" => "HTML"
				]
			);
		} elseif ($this->info["status"] === "recording") {
			Exe::sendMessage(
				[
					"text" => $this->lang->get("kulgram.error.start_when_recording"),
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"],
					"parse_mode" => "HTML"
				]
			);
		}

		return true;
	}

	/**
	 * @return bool
	 */
	private function init(string $title, string $author): bool
	{
		if ($this->loadData()) {
			return true;
		}

		if ($this->info["status"] === "sleep") {
			$this->info["status"] = "idle";
			$this->info["count"]++;
			$this->info["session"] = [
				"title" => $title,
				"author" => $author
			];

			Exe::sendMessage(
				[
					"text" => $this->lang->bind(
						[
							"session_id" => $this->info["count"],
							"title" => ee($title),
							"author" => ee($author)
						],
						$this->lang->get("kulgram.run.init_ok")
					),
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"],
					"parse_mode" => "HTML"
				]
			);
		} elseif ($this->info["status"] === "idle") {
			Exe::sendMessage(
				[
					"text" => (
						$this->lang->bind(
							[
								"session_id" => $this->info["count"],
								"title" => ee($this->info["session"]["title"]),
								"author" => ee($this->info["session"]["author"])
							],
							$this->lang->get("kulgram.error.init_idle")
						)
						."\n\n"
						.$this->lang->get("kulgram.usage.footer")
					),
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"],
					"parse_mode" => "HTML"
				]
			);
		} elseif ($this->info["status"] === "recording") {
			Exe::sendMessage(
				[
					"text" => (
						$this->lang->bind(
							[
								"session_id" => $this->info["count"],
								"title" => ee($this->info["session"]["title"]),
								"author" => ee($this->info["session"]["author"])
							],
							$this->lang->get("kulgram.error.init_recording")
						)
						."\n\n"
						.$this->lang->get("kulgram.usage.footer")
					),
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"],
					"parse_mode" => "HTML"
				]
			);
		}

		return true;
	}

	/**
	 * @return bool
	 */
	private function stop(): bool
	{
		if ($this->loadData()) {
			return true;
		}
	}

	/**
	 * Destructor.
	 */
	public function __destruct()
	{
		is_resource($this->handle) and fclose($this->handle);
		file_put_contents(
			$this->path."/info.json",
			json_encode($this->info, JSON_UNESCAPED_SLASHES),
			LOCK_EX
		);
	}
}
