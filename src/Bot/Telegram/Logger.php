<?php

namespace Bot\Telegram;

use Bot\Telegram\Logger\User as UserLogger;
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
	 * @var \PDO
	 */
	private $pdo;

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
		if ($this->data["chat_type"] === "group") {
			$this->groupMessageLogger();
		} else {
			$this->privateMessageLogger();
		}
	}

	/**
	 * @return void
	 */
	private function groupMessageLogger(): void
	{
		$this->pdo = DB::pdo();
		$st = new UserLogger($this->data);
		$st->run();
	}

	/**
	 * @return void
	 */
	private function privateMessageLogger(): void
	{
	}
}
