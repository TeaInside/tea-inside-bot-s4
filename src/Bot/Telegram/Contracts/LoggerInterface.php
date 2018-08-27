<?php

namespace Bot\Telegram\Contracts;

use Bot\Telegram\Data;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram
 * @version 4.0
 */
interface LoggerInterface
{
	/**
	 * @param \Bot\Telegram\Data $data
	 *
	 * Constructor.
	 */
	public function __construct(Data $data);

	/**
	 * @return void
	 */
	public function run(): void;
}
