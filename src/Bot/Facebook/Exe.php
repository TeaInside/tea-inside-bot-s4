<?php

namespace Bot\Facebook;

use Bot\Facebook\Exceptions\InvalidJsonDataException;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Facebook
 * @license MIT
 * @since 0.0.1
 */
final class Exe
{
	const GRAPH_URL = "https://graph.facebook.com/";

	/**
	 * @param string
	 */
	private $token;

	/**
	 * @param string $token
	 *
	 * Constructor.
	 */
	public function __construct(string $token)
	{
		$this->token = $token;
	}

	/**
	 * @param string $method
	 * @param array  $input
	 * @return array
	 */
	public function post(string $method, array $input = [])
	{
		return $this->exec(
			self::GRAPH_URL.$method."?access_token={$this->token}",
			[
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => json_encode($input),
				CURLOPT_HTTPHEADER => [
					"Content-Type: application/json"
				]
			]
		);
	}

	/**
	 * @param string $method
	 * @param array  $input
	 * @return array
	 */
	public function get(string $method, array $input = [])
	{
		return $this->exec(
			self::GRAPH_URL.$method."?access_token={$this->token}&".http_build_query($input)
		);
	}

	/**
	 * @param string $url
	 * @param array  $opt
	 * @return array
	 */
	public function exec(string $url, array $opt = []): array
	{
		$optf = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false
		];

		foreach ($opt as $key => $value) {
			$optf[$key] = $value;
		}

		$ch = curl_init($url);
		curl_setopt_array($ch, $optf);
		$out = curl_exec($ch);
		$info = curl_getinfo($ch);
		$err = curl_error($ch);
		$ern = curl_errno($ch);
		curl_close($ch);

		return [
			"out" => $out,
			"error" => $err,
			"errno" => $ern,
			"info" => $info
		];
	}
}
