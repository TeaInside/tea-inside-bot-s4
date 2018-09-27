<?php

namespace Isolator\Contracts;

interface Interpreter
{	
	/**
	 * @param string $code
	 * @param int    $boxId
	 *
	 * Constructor.
	 */
	public function __construct(string $code, int $boxId);

	/**
	 * @return void
	 */
	public function run(): void;

	/**
	 * @return string
	 */
	public function getResult(): string;
}
