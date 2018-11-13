<?php

namespace Bot\Telegram\Responses;

use Bot\Telegram\Exe;
use Bot\Telegram\ResponseFoundation;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Bot\Telegram\Responses
 * @version 4.0
 */
class Admin extends ResponseFoundation
{
	/**
	 * @return bool
	 */
	public function call(): bool
	{
		$r = "";
		$a = Exe::getChatAdministrators(["chat_id" => $this->data["chat_id"]]);
		$a = json_decode($a["out"], true);
		var_dump($a);die;
		if (isset($a["result"])) {
			foreach ($a["result"] as $key => $admin) {				
			}
		}

		return true;
	}
}
