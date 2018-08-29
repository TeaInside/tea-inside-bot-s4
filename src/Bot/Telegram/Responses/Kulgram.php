<?php

namespace Bot\Telegram\Responses;

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
					break;
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
								"reply_to_message_id" => $this->data["msg_id"]
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
								"reply_to_message_id" => $this->data["msg_id"]
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
								"reply_to_message_id" => $this->data["msg_id"]
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
								"reply_to_message_id" => $this->data["msg_id"]
							]
						);
					return true;
					break;
			}

		}
	}

	/**
	 * @throws \Exception
	 * @return void
	 */
	private function loadData(): void
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
	}

	/**
	 * Destructor.
	 */
	public function __destruct()
	{
		fclose($this->handle);
		file_put_contents(
			$this->path."/info.json",
			json_encode($this->info, JSON_UNESCAPED_SLASHES),
			LOCK_EX
		);
	}

	/**
	 * @return bool
	 */
	private function init(string $title, string $author): bool
	{
		$this->loadData();

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
							"title" => $title,
							"author" => $author
						],
						$this->lang->get("kulgram.run.init_ok")
					),
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
		} elseif ($this->info["status"] === "idle") {
			Exe::sendMessage(
				[
					"text" => (
						$this->lang->bind(
							[
								"session_id" => $this->info["count"],
								"title" => $this->info["session"]["title"],
								"author" => $this->info["session"]["author"]
							],
							$this->lang->get("kulgram.error.init_idle")
						)
						."\n\n"
						.$this->lang->get("kulgram.usage.footer")
					),
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
		} elseif ($this->info["status"] === "recording") {
			Exe::sendMessage(
				[
					"text" => (
						$this->lang->bind(
							[
								"session_id" => $this->info["count"],
								"title" => $this->info["session"]["title"],
								"author" => $this->info["session"]["author"]
							],
							$this->lang->get("kulgram.error.init_start")
						)
						."\n\n"
						.$this->lang->get("kulgram.usage.footer")
					),
					"chat_id" => $this->data["chat_id"],
					"reply_to_message_id" => $this->data["msg_id"]
				]
			);
		}

		return true;
	}
}
