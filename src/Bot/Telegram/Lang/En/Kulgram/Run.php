<?php

namespace Bot\Telegram\Lang\En\Kulgram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram\Lang\En\Kulgram
 * @version 4.0
 */
class Run
{
	static $list = [
		"init_ok" => 
"A kulgram session has been initialized!

Session ID	: {{session_id}}
Title		: {{title}}
Author		: {{author}}

Use <code>/kulgram start</code> to start the kulgram recorder.",
		"start" => "System record has been started!

Kindly to start the kulgram.",
		"cancel" => "Session has been cancelled.",
		"stop_1" => "Stopping system record...",
		"stop_2" => "System record stopped successfully!",
		"stop_building_pdf" => "Building PDF data..."
	];
}