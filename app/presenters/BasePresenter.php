<?php

namespace App\Presenters;

use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Presenter;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter {

	/** @var EntityManager @inject */
	public $em;

	public function startup() {
		parent::startup();
		/** Redirect unlogged users to homepage */
		if (!$this->user->isLoggedIn()) {
			if ($this->name !== 'Sign' && ($this->action !== 'in' || $this->action !== 'register')) {
				$this->redirect('Sign:in');
			}
		}
	}

}
