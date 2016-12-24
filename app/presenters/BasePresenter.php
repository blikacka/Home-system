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

}
