<?php

namespace Bot\Telegram;

use Bot\Telegram\Contracts\Response as ResponseContract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram
 * @version 4.0
 */
abstract class ResponseFoundation implements ResponseContract
{
	/**
	 * @var \Bot\Telegram\Data
	 */
	protected $data;

	/**
	 * @param \Bot\Telegram\Data $data
	 *
	 * Constructor.
	 */
	public function __construct(Data $data)
	{
		$this->data = $data;
	}
}
