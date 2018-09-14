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
	 * @var \Bot\Facebook\Data
	 */
	protected $data;

	/**
	 * @param \Bot\Facebook\Data $data
	 *
	 * Constructor.
	 */
	public function __construct(Data $data)
	{
		$this->data = $data;
	}
}
