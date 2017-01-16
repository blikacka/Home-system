<?php

namespace App\Services\Helpers;

/**
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 */
class Base64Macro {
	/**
	 * @param string $str
	 * @return mixed
	 */
	public function __invoke($str) {

		return base64_encode($str);

	}
}


