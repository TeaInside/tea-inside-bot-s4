<?php

namespace Bot\Facebook;

use Bot\Facebook\Lang\Map;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Facebook
 * @version 4.0
 */
final class Lang
{
	/**
	 * @var string
	 */
	private $lang = "En";

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @param array $data
	 *
	 * Constructor
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get(string $key)
	{
		$key = explode(".", $key, 3);
		if (count($key) === 3) {
			$m = $key[0].".".$key[1];
			if (isset(Map::$map[$m])) {
				if (isset(Map::$map[$m]::$list[$key[2]])) {
					return Map::$map[$m]::$list[$key[2]];
				}
			}
		}
		return null;
	}

	/**
	 * @param array $binder
	 * @param string $subject
	 * @return string
	 */
	public function bind(array $binder, string $subject): string
	{
		$r1 = $r2 = [];

		foreach ($binder as $key => $value) {
			$r1[] = "{{".$key."}}";
			$r2[] = $value;
		}
		unset($binder, $key, $value);

		return str_replace($r1, $r2, $subject);
	}
}
