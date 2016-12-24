<?php


namespace App\Presenters;
use App\Forms\UserForm;


/**
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 */

class UserPresenter extends BasePresenter {

	/** @var UserForm @inject */
	public $userForm;

	public function createComponentUpdateUser() {
		return $this->userForm->create(function () {
			$this->flashMessage('Účet byl upraven');
			$this->redirect('this');
		}, $this->user->identity);
	}

}