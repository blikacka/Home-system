<?php

namespace App\Services;

use App\Entities\Role;
use App\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;
use Nette\SmartObject;
use Nette\Security;

/**
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 */
class UserService {
	use SmartObject;

	/** @var EntityManager */
	public $em;

	/** @var Security\User */
	protected $securityUser;

	public function __construct(EntityManager $em, Security\User $securityUser) {
		$this->em = $em;
		$this->securityUser = $securityUser;
	}

	/**
	 * @param User|null   $user
	 * @param mixed|array $values
	 * @throws DuplicateUserException
	 */
	public function userAddEdit($user, $values) {
		$isNewUser = false;

		if ($user == null) {
			if (($this->em->getRepository(User::class)
			              ->findOneBy(['email' => $values->email])) != null) {
				throw new DuplicateUserException();
			}
			$isNewUser = true;
			$user = new User();
			$user->role = $this->em->find(Role::class, Role::USER);

		} else {
			$user = $this->em->getReference(User::class, $user->id);
		}

		$user->name = $values->name;
		$user->email = $values->email;

		if ($values->password != null) {
			$user->password = Passwords::hash($values->password);
		}

		$this->em->persist($user);
		$this->em->flush();

		if ($isNewUser) {
			$this->securityUser->login($values->email, $values->password);
		}
	}

}

class DuplicateUserException extends \Exception {}