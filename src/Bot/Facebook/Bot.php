<?php

namespace Bot\Telegram;

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
		Singleton::init(
			[
				"data" => $this->data,
				"lang" => [Lang::class, [$this->data]]
			]
		);
	}

	/**
	 * @return void
	 */
	public function run(): void
	{
		$st = new Response($this->data);
		$st->run();
	}
}
