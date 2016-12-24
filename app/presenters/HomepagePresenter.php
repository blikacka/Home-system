<?php

namespace App\Presenters;

use App\Services\GetCamPic;
use Nette;

class HomepagePresenter extends BasePresenter {

	public function renderDefault() {
		$this->template->anyVariable = 'any value';
	}

	public function renderCamPic() {
		$this->template->cam = (new GetCamPic())->getPic();
	}

}
