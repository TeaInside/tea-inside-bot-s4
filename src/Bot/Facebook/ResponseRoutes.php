<?php

namespace Bot\Facebook;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Facebook
 * @version 4.0
 */
trait ResponseRoutes
{
	/**
	 * @return void
	 */
	private function buildRoutes(): void
	{
		if (isset($this->data["message"]["text"])) {
			$txt = $this->data["message"]["text"];
		} else {
			$txt = null;
		}

		/**
		 * Ping cmd
		 */
		$this->set(function () use ($txt) {
			return [
				(bool) preg_match("/^(\!|\/|\~|\.)?ping$/Usi", $txt),
				[]
			];
		}, "Ping@ping");

		/**
		 * Help cmd
		 */
		$this->set(function () use ($txt) {
			return [
				(bool) preg_match("/^(\!|\/|\~|\.)?help$/Usi", $txt),
				[]
			];
		}, "Help@menu");

		/**
		 * Shell exec cmd
		 */
		$this->set(function () use ($txt) {
			return [
				(bool) preg_match("/^(\!|\/|\~|\.)?sh[\s\n]*.{1,}$/Usi", $txt),
				[]
			];
		}, "Sh@shell");

		/**
		 * Calculator cmd
		 */
		$this->set(function () use ($txt) {
			return [
				(bool) preg_match("/^(\!|\/|\~|\.)?calc/Usi", $txt),
				[]
			];
		}, "Calculator@calc");
	}
}
