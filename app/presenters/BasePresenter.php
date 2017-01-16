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

	/** @persistent */
	public $cronHash;

	public function startup() {
		parent::startup();
		/** Redirect unlogged users to homepage */
		if (!$this->user->isLoggedIn()) {
			if (($this->name !== 'Sign' && ($this->action !== 'in' || $this->action !== 'register')) || $this->cronHash !== 'cb7ddd82ced9a4e1afd7abcf13cd8b862475bf55cc8feba0bf95d1fc03bdc536') {
				$this->redirect('Sign:in');
			}
		}
	}

}
