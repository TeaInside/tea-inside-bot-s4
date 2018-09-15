<?php

namespace Bot\Facebook;

use ArrayAccess;
use JsonSerializable;
use Bot\Facebook\Exceptions\InvalidJsonDataException;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Facebook
 * @license MIT
 * @since 0.0.1
 */
final class Data implements ArrayAccess, JsonSerializable
{
	private $__;

	/**
	 * @var array
	 */
	public $in = [];

	/**
	 * @var array
	 */
	private $container = [];

	/**
	 * @param string $jsonString
	 * @throws \Bot\Facebook\Exceptions\InvalidJsonDataException
	 * @return void
	 *
	 * Constructor.
	 */
	public function __construct(string $jsonString)
	{
		$this->in = json_decode($jsonString, true);
		if (! is_array($this->in)) {
			throw new InvalidJsonDataException(
				"The result of json_decode must be an array"
			);
		}
		$this->buildContainer();
		$this["_now"] = date("Y-m-d H:i:s");
	}

	/**
	 * @return void
	 */
	private function buildContainer()
	{
		if (
			isset($this->in["entry"], $this->in["object"]) && 
			$this->in["object"] === "page" && is_array($this->in["entry"])
		) {
			$this["entry"] = [];
			foreach ($this->in["entry"] as $key => $v) {
				$this["object"] = "page";
				$this["entry"][$key] = $v;;
			}
		}
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->container[$offset] = $value;
	}

	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function &offsetGet($offset)
	{
		if ($this->offsetExists($offset)) {
			return $this->container[$offset];
		} else {
			return $this->__;
		}
	}

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->container) && !is_null($this->container[$offset]);
	}

	/**
	 * @param mixed $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->container[$offset]);
	}

	/**
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->container;
	}
}
