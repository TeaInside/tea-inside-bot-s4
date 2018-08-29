<?php

namespace Bot\Telegram;

use Closure;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram
 * @version 4.0
 */
final class Response
{
	use ResponseRoutes;

	/**
	 * @var \Bot\Telegtram\Data
	 */
	private $data;

	/**
	 * @var array
	 */
	private $routes = [];

	/**
	 * @param \Bot\Telegtram\Data $data
	 */
	public function __construct(Data $data)
	{
		$this->data = $data;
	}

	/**
	 * @return void
	 */
	public function run(): void
	{
		$this->buildRoutes();
		foreach ($this->routes as $key => $route) {
			$route[0] = $route[0]();
			if (isset($route[0], $route[1]) && is_array($route[1])) {
				if ($route[0][0]) {
					if (is_string($route[1])) {
						
					} elseif ($route[1] instanceof Closure) {
						if ($route[1](...$route[1])) {
							break;		
						}
					}
				}
			}
		}
	}

	/**
	 * @param \Closure $cond
	 * @param mixed    $action
	 * @return void
	 */
	private function set(Closure $cond, $action): void
	{
		$this->routes[] = [$cond, $action];
	}
}
