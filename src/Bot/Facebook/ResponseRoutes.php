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

		/**
		 * Time cmd
		 */
		$this->set(function () use ($txt) {
			return [
				(bool) preg_match("/^(\!|\/|\~|\.)?time/Usi", $txt),
				[]
			];
		}, "Time@showTime");

		/**
		 * Translate command
		 * 
		 * Example: ["/tr en id How are you?", "!tr en id What time is it?"]
		 */
		$this->set(function($d) use ($txt) {
			return [
				(bool) preg_match("/^(\!|\/|\~|\.)?t(l|r)($|[\s\n])/Usi", $txt),
				[]
			];
		}, "Translate@googleTranslate");
	}
}
