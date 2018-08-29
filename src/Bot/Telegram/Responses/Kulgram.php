<?php

namespace Bot\Telegram\Responses;

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
	 * @return bool
	 */
	public function handle(): bool
	{
		$singleton = Singleton::getInstance();
		$this->lang = $singleton->get("lang");
		unset($singleton);

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
					if ((!isset($_argv["title"])) && (!isset($_argv["author"]))) {
						Exe::sendMessage(
							[
								"chat_id" => $this->data["chat_id"],
								"text" => $this->lang->get("kulgram.error.init_no_title_no_author"),
								"reply_to_message_id" => $this->data["msg_id"]
							]
						);
						return true;
					}

					if (! isset($_argv["title"])) {
						Exe::sendMessage(
							[
								"chat_id" => $this->data["chat_id"],
								"text" => ""
							]
						);
						return true;
					}

					if (! isset($_argv["author"])) {
						Exe::sendMessage(
							[
								"chat_id" => $this->data["chat_id"],
								"text" => ""
							]
						);
						return true;
					}

					return $this->init($_argv["title"]." oleh ".$_argv["author"]);
				case "start":
					return $this->start();
				case "stop":
					return $this->stop();
				default:
				Exe::sendMessage(
							[
								"chat_id" => $this->data["chat_id"],
								"text" => self::USAGE_1,
								"reply_to_message_id" => $this->data["msg_id"]
							]
						);
					return true;
					break;
			}

		}
	}
}
