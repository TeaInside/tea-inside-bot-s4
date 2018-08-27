<?php

namespace Bot\Telegram;

use Bot\Telegram\Logger\User as UserLogger;
use Bot\Telegram\Logger\Group as GroupLogger;
use Bot\Telegram\Contracts\LoggerInterface;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram
 * @version 4.0
 */
final class Logger
{
	/**
	 * @var \Bot\Telegram\Data
	 */
	private $data;

	/**
	 * @param string $json
	 *
	 * Constructor.
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
		if (in_array($this->data["msg_type"], ["text"])) {
			if ($this->data["chat_type"] === "group") {
				$this->groupMessageLogger();
			} else {
				$this->privateMessageLogger();
			}
		}
	}

	/**
	 * @return void
	 */
	private function groupMessageLogger(): void
	{
		$st = new UserLogger($this->data);
		$st->run();
		$st = new GroupLogger($this->data);
		$st->run();
	}

	/**
	 * @return void
	 */
	private function privateMessageLogger(): void
	{
	}
}
