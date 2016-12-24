<?php

namespace App\Presenters;

use App\Forms\SignInForm;
use App\Forms\UserForm;
use Nette;


class SignPresenter extends BasePresenter {

	/** @var SignInForm @inject */
	public $signInForm;

	/** @var UserForm @inject */
	public $userForm;

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm() {
		return $this->signInForm->create(function () {
			$this->redirect('Homepage:');
		});
	}

	protected function createComponentRegisterForm() {
		return $this->userForm->create(function () {
			$this->redirect('Homepage:');
		});
	}

	public function actionOut() {
		$this->getUser()
		     ->logout();
	}

}
