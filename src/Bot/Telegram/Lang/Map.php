<?php

namespace Bot\Telegram\Lang;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram\Lang
 * @version 4.0
 */
final class Map
{
	public static $map = [
		"kulgram.run" => En\Kulgram\Run::class,
		"kulgram.error" => En\Kulgram\Error::class,
		"kulgram.usage" => En\Kulgram\Usage::class,
		"translate.usage" => En\Translate\Usage::class
	];
}
