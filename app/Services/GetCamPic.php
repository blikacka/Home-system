<?php


namespace App\Services;


/**
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 */

class GetCamPic {

	const DEFAULT_IP = '10.10.10.20';
	const DEFAULT_PORT = 8080;
	const PIC_PATH = '/cam_1.jpg';

	/** @var null|string */
	public $ip;

	/** @var int|null */
	public $port;

	/** @var null|string */
	public $picPath;


	/**
	 * GetCamPic constructor.
	 * @param null|string $ip
	 * @param null|int    $port
	 * @param null|string $picPath
	 */
	public function __construct($ip = null, $port = null, $picPath = null) {
		$this->ip = $ip ?? self::DEFAULT_IP;
		$this->port = $port ?? self::DEFAULT_PORT;
		$this->picPath = $picPath ?? self::PIC_PATH;
	}

	/**
	 * Echo getted pic
	 * @return string
	 */
	public function getPic() {
		$host = $this->ip;
		$port = $this->port;
		$path = $this->picPath; //or .dll, etc. for authnet, etc.
		$poststring = "";
		$poststring = substr($poststring, 0, -1);
		$img = "";

		$fp = fsockopen($host, $port, $errno, $errstr, $timeout = 30);

		if (!$fp) {
			echo "$errstr ($errno)\n";
		} else {

			//send the server request
			fputs($fp, "POST $path HTTP/1.1\r\n");
			fputs($fp, "Host: $host\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: " . strlen($poststring) . "\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $poststring . "\r\n\r\n");
			$im = 0;
			while (!feof($fp)) {
				$img .= fgets($fp, 4096);
			}

			$sourceimg = strchr($img, "\r\n\r\n"); //removes headers
			$sourceimg = ltrim($sourceimg); //remove whitespaces from begin of the string
			$img = base64_encode($sourceimg);
			fclose($fp);
		}
		echo $img;

	}
}