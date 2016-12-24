<?php


namespace App\Forms;

use App\Entities\User;
use App\Services\DuplicateUserException;
use App\Services\UserService;
use Nette\Application\UI\Form;
use Nette\SmartObject;


/**
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 */
class UserForm {
	use SmartObject;

	const PASSWORD_MIN_LENGTH = 7;

	/** @var FormFactory */
	private $factory;

	/** @var UserService */
	protected $userService;

	public function __construct(FormFactory $factory, UserService $userService) {
		$this->factory = $factory;
		$this->userService = $userService;
	}

	/**
	 * @param callable  $onSuccess
	 * @param User|null $user
	 * @return Form
	 */
	public function create(callable $onSuccess, User $user = null) {
		$form = $this->factory->create();

		$form->addText('name', 'Jméno')
		     ->setRequired('Prosím vyplňte jméno');

		$form->addText('email', 'Email')
		     ->setRequired('Prosím vyplňte email')
		     ->addRule($form::EMAIL);

		$form->addPassword('password', 'Heslo');

		$form->addSubmit('send', $user == null ? 'Registrovat' : 'Upravit');

		if ($user != null) {
			$form->setDefaults(['name' => $user->name, 'email' => $user->email]);
		} else {
			$form['password']->setOption('description', sprintf('Heslo musí obsahovat alespoň %d znaků', self::PASSWORD_MIN_LENGTH))
			                 ->setRequired('Prosím vyplňte heslo')
			                 ->addRule($form::MIN_LENGTH, null, self::PASSWORD_MIN_LENGTH);
		}

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess, $user) {
			try {
				$this->userService->userAddEdit($user, $values);
			} catch (DuplicateUserException $e) {
				$form->addError('Tento email je již zaregistrován!');
				return;
			}
			$onSuccess();
		};
		return $form;
	}


}