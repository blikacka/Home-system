<?php

namespace App\Presenters;

use App\Services\GetCamPic;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Nette;
use Tracy\Debugger;

class HomepagePresenter extends BasePresenter {

	/** @var array */
	protected $vrchyParams;

	public function startup() {
		parent::startup();
		header("Pragma-directive: no-cache");
		header("Cache-directive: no-cache");
		header("Cache-control: no-cache");
		header("Pragma: no-cache");
		header("Expires: 0");
	}

	public function beforeRender() {
		parent::beforeRender();
		$this->vrchyParams = $this->context->getParameters()['vrchy'] ?? null;
		$this->template->vrchyParams = $this->vrchyParams;
	}

	public function renderDefault() {
		$this->template->anyVariable = 'any value';
	}

	public function renderCamPic() {
		$this->template->cam = (new GetCamPic())->getPic();
	}

	public function renderVrchyPicUno() {
		$this->template->camUno = (new GetCamPic($this->vrchyParams['URL'], $this->vrchyParams['PORT'], '/cam_1.jpg'))->getPic();
	}

	public function renderVrchyPicDuo() {
		$this->template->camDuo = (new GetCamPic($this->vrchyParams['URL'], $this->vrchyParams['PORT'], '/cam_2.jpg'))->getPic();
	}

	public function renderVrchyGal() {
		$client = new Client();
		$publicKey = $this->vrchyParams['gallery']['headers']['publicKey'];
		$secretKey = $this->vrchyParams['gallery']['headers']['privateKey'];
		$timestamp = time();
		try {
			$this->template->vyrchiGal = ($client->get($this->vrchyParams['gallery']['fullpath'], [
				'headers' => [
					'Logskynet' => $publicKey,
					'Logskynettime' => $timestamp,
					'Logskynetsecret' => sha1($publicKey . $secretKey . $timestamp)
				],
				'timeout' => 5
			])
			                                     ->getBody()
			                                     ->getContents());
		} catch (ConnectException $ex) {
			$this->template->vyrchiGal = 'Nepodařilo se načíst galerii, zkus to prosím znova později.';
		}

	}

	public function renderVrchyGalMain() {
		$this->template->mainGalPath = $this->vrchyParams['gallery']['pathMainGallery'];
	}

}
