<?php

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 4.0
 */
final class Singleton
{
	/**
	 * @var self
	 */
	private static $self;

	/**
	 * @var array
	 */
	private $instances = [];

	/**
	 * @param array $initClass
	 *
	 * Constructor.
	 */
	private function __construct(array $instances)
	{
		$this->instances = $instances;
	}

	/**
	 * @param array $instances
	 * @return void
	 */
	public static function init(array $instances): void
	{
		self::$self = new self($instances);
	}

	/**
	 * @throws \Exception
	 * @return \Singleton
	 */
	public static function getInstance(): object
	{
		if (! self::$self) {
			throw new Exception("Invalid singleton instantiator");
		}
		return self::$self;
	}

	/**
	 * @param string 		$key
	 * @param string|object	$instance
	 * @return void
	 */
	public function set(string $key, $instance): void
	{
		$this->instances[$key] = $instance;
	}

	/**
	 * @param string $key
	 * @throws \Exception
	 * @return object
	 */
	public function get(string $key): object
	{
		if (isset($this->instances[$key])) {
			if (is_array($this->instances[$key])) {
				$this->instances[$key] = new $this->instances[$key][0](...$this->instances[$key][1]);
			} elseif (! is_object($this->instances[$key])) {
				throw new Exception("Invalid instance value");
			}
			return $this->instances[$key];
		} else {
			throw new Exception("Invalid instance get key \"{$key}\"");
		}
	}
}
