<?php

namespace Bot\Facebook;

use Singleton;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram
 * @version 4.0
 */
final class Bot
{
	/**
	 * @param string $json
	 *
	 * Constructor
	 */
	public function __construct(string $json)
	{
		$this->data = new Data($json);
	}

	/**
	 * @return void
	 */
	public function run(): void
	{
		if (isset($this->data["entry"]) && is_array($this->data["entry"])) {
			foreach ($this->data["entry"] as $key => $v) {
				if (isset($v["messaging"]) && is_array($v["messaging"])) {
					foreach ($v["messaging"] as $vv) {
						if (isset($vv["message"]["text"])) {
							Singleton::init(
								[
									"lang" => [Lang::class, [$vv]]
								]
							);
							$st = new Response($vv);
							$st->run();
						}
					}
				}
			}
		}
	}
}
