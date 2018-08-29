<?php

namespace Bot\Telegram\Lang\En\Kulgram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram\Lang\En\Kulgram
 * @version 4.0
 */
class Error
{
	static $list = [
		"init_no_title_no_author" => "krec: fatal error: You need to provide the title and author.",
		"init_no_title" => "krec: fatal error: You need to provide the title.",
		"init_no_author" => "krec: fatal error: You need to provide the author.",
		"init_idle" => "Could not initialize system record because there is an idle session. Please cancel or finish this session!

**Current Session**
Session ID	: {{session_id}}
Title		: {{title}}
Author		: {{author}}",
		"init_start" => "Could not initialize system record because there is an active session. Please stop this session first!

**Current Session**
Session ID	: {{session_id}}
Title		: {{title}}
Author		: {{author}}",
		"group_only" => "You can only use this command in the group.",
	];
}
