<?php

namespace Bot\Facebook\Responses;

use Singleton;
use Exception;
use Bot\Facebook\Exe;
use Bot\Facebook\ResponseFoundation;
use GoogleTranslate\GoogleTranslate;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Facebook\Responses
 * @version 4.0
 */
class Translate extends ResponseFoundation
{
	/**
	 * @return void
	 */
	public function googleTranslate(): bool
	{
		defined("data") or define("data", STORAGE_PATH);
		var_dump($this->data["message"]["text"]);
		if (preg_match("/^(?:\!|\/|\~|\.)?(?:t)(?:r|l)(?:[\s\n]+)([a-z]{2}|auto|zh-cn|zh-tw)(?:[\s\n]+)([a-z]{2}|zh-cn|zh-tw)(?:[\s\n]+)?(.*)?$/Usi", $this->data["message"]["text"], $m)) {
			if (isset($m[3])) {

				try {
					$st = new GoogleTranslate(trim($m[3]), $m[1], $m[2]);
					$st = trim($st->exec());
					$st = $st === "" ? "~" : $st;
				} catch (Exception $e) {
					$st = $e->getMessage()."\nSee available languages at: https://github.com/ammarfaizi2/GoogleTranslate/blob/master/README.md";
				}
				
				$std = new Exe(TOKENS["EsTehkuSegar"]);

				$out = $std->post("v2.6/me/messages", [
					"recipient" => [
						"id" => $this->data["sender"]["id"]
					],
					"message" => [
						"text" => $st
					]
				]);
				return true;
			}
		}

		$singleton = Singleton::getInstance();
		$lang = $singleton->get("lang");
		unset($singleton);
		$st = new Exe(TOKENS["EsTehkuSegar"]);
		$out = $st->post("v2.6/me/messages", [
			"recipient" => [
				"id" => $this->data["sender"]["id"]
			],
			"message" => [
				"text" => $lang->get("translate.usage.basic"),
			]
		]);
		var_dump(111,111,$out["out"]);
		return true;
	}
}
