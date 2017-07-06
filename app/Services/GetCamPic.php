<?php


namespace App\Services;

use GuzzleHttp\Client;


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
		$uri = $this->ip . ($this->port !== null ? ':' . $this->port : '') . ($this->picPath !== null ? '/' . $this->picPath : '');
		$client = new Client();
		$response = $client->get($uri, [
			'headers' => [
				'Host' => $this->ip,
				'Content-type' => 'application/x-www-form-urlencoded',
			],
			'timeout' => 5
		])
		                   ->getBody()
		                   ->getContents();
		echo utf8_decode(base64_encode($response));

	}
}