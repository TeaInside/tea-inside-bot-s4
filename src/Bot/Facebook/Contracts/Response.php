<?php

namespace Bot\Facebook\Contracts;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Facebook\Contract
 * @version 4.0
 */
interface Response
{
	/**
	 * @param array $data
	 *
	 * Constructor.
	 */
	public function __construct(array $data);
}
