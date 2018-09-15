<?php

namespace Bot\Facebook;

use Bot\Facebook\Contracts\Response as ResponseContract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Facebook
 * @version 4.0
 */
abstract class ResponseFoundation implements ResponseContract
{
	/**
	 * @var array
	 */
	protected $data;

	/**
	 * @param array $data
	 *
	 * Constructor.
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}
}
