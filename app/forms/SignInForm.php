<?php

namespace App\Forms;

use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\SmartObject;


class SignInForm {
	use SmartObject;

	/** @var FormFactory */
	private $factory;

	/** @var User */
	private $user;

	public function __construct(FormFactory $factory, User $user) {
		$this->factory = $factory;
		$this->user = $user;
	}

	/**
	 * @param callable $onSuccess
	 * @return Form
	 */
	public function create(callable $onSuccess) {
		$form = $this->factory->create();
		$form->addText('username', 'Email')
		     ->setRequired('Zadejte email');

		$form->addPassword('password', 'Heslo')
		     ->setRequired('Zadejte heslo');

		$form->addCheckbox('remember', 'Zapamatovat přihlášení');

		$form->addSubmit('send', 'Přihlásit se');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			try {
				$this->user->setExpiration($values->remember ? '14 days' : '20 minutes');
				$this->user->login($values->username, $values->password);
			} catch (\Exception $e) {
				$form->addError($e->getMessage());
				return;
			}
			$onSuccess();
		};
		return $form;
	}

}
