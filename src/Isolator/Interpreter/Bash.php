<?php

namespace Isolator\Interpreter;

use Isolator\Isolator;
use Isolator\Contracts\Interpreter as InterpreterContract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 * @package Isolator\Interpreter
 */
class Bash implements InterpreterContract
{	
	/**
	 * @var string
	 */
	private $boxId = 0;

	/**
	 * @var string $code
	 */
	private $code;

	/**
	 * @var string
	 */
	private $result;

	/**
	 * @param string $code
	 * @param int    $boxId
	 *
	 * Constructor.
	 */
	public function __construct(string $code, int $boxId)
	{
		$this->boxId = $boxId;
		$this->code  = $code;
	}

	/**
	 * @return void
	 */
	public function run(): void
	{
		$st = new Isolator($this->boxId);

		$uid = $st->getUid();
		
		is_dir(ISOLATOR_USER_DIR."/u{$uid}/home/u{$uid}/scripts") or mkdir(ISOLATOR_USER_DIR."/u{$uid}/home/u{$uid}/scripts");
		is_dir(ISOLATOR_USER_DIR."/u{$uid}/home/u{$uid}/scripts/bash") or mkdir(ISOLATOR_USER_DIR."/u{$uid}/home/u{$uid}/scripts/bash");
		
		$filename = substr(md5($this->code), 0, 5).".sh";
		if (!file_exists(ISOLATOR_USER_DIR."/u{$uid}/home/u{$uid}/scripts/php/{$filename}")) {
			file_put_contents(ISOLATOR_USER_DIR."/u{$uid}/home/u{$uid}/scripts/php/{$filename}", $this->code);
		}

		$st->maxProcesses(5);
		$st->extraTime(3);
		$st->maxWallTime(15);
		$st->maxExecutionTime(15);
		$st->memoryLimit(1024 * 512);

		$st->run("/usr/bin/env bash -c ".escapeshellarg($this->code));

		$this->result = trim($st->getResult()); // ."\n\n".trim($st->getIsolateOut());
	}

	/**
	 * @return string
	 */
	public function getResult(): string
	{
		return $this->result;
	}
}
