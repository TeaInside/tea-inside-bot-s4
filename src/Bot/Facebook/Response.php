<?php

namespace Bot\Facebook;

use Closure;
use Bot\Facebook\Exceptions\ResponseException;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Facebook
 * @version 4.0
 */
final class Response
{
	use ResponseRoutes;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @var array
	 */
	private $routes = [];

	/**
	 * @param array $data
	 *
	 * Constructor.
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}

	/**
	 * @throws \Bot\Facebook\Exceptions\ResponseException
	 * @return void
	 */
	public function run(): void
	{
		$this->buildRoutes();
		foreach ($this->routes as $key => $route) {
			$route[0] = $route[0]($this->data);
			if (isset($route[0], $route[0][1]) && is_array($route[0][1])) {
				if ($route[0][0]) {
					if (is_string($route[1])) {
						$route[1] = explode("@", $route[1]);
						if (count($route[1]) === 2) {
							$route[1][0] = "\\Bot\\Telegram\\Responses\\".$route[1][0];
							$route[1][0] = new $route[1][0]($this->data);
							if (call_user_func_array(
								[
									$route[1][0], 
									$route[1][1]
								],
								$route[0][1]
							)) {
								break;
							}
						} else {
							throw new ResponseException("Invalid action \"".implode("@", $route[1])."\"");
						}
					} elseif ($route[1] instanceof Closure) {
						if ($route[1](...$route[0][1])) {
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
