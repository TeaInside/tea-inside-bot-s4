<?php

namespace Isolator;

use Exception;
use Isolator\Interpreter\Php;
use Isolator\Interpreter\Bash;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Isolator
 * @version 4.0
 */
final class Virtualizor
{
	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var int
	 */
	private $boxId = 0;

	/**
	 * @var object
	 */
	private $st;

	/**
	 * @var string
	 */
	private $result = "";

	/**
	 * Constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * @param string $id
	 * @return void
	 */
	public function setId(string $id): void
	{
		$this->boxId = Isolator::generateBoxId($id);
	}

	/**
	 * @param string $code
	 * @param string $bin
	 * @return void
	 */
	public function run(string $code, string $bin): void
	{
		switch ($bin) {
			case "php":
				$st = new Php($code, $this->boxId);
				break;
			case "bash":
				$st = new Bash($code, $this->boxId);
				break;
			default:
				break;
		}
		
		$st->run();
		$this->result = $st->getResult();;
	}

	/**
	 * @return string
	 */
	public function getResult(): string
	{
		return $this->result;
	}
}
