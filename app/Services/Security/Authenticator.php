<?php


namespace App\Services\Security;

use App\Entities\Role;
use App\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\AbortException;
use Nette\Object;
use Nette\Security\IAuthenticator;
use Nette\Security;
use Nette\SmartObject;

/**
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 */
class Authenticator implements IAuthenticator {

	use SmartObject;

	/** @var EntityManager */
	public $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	public function authenticate(array $credentials) {

		list($email, $password) = $credentials;

		/** @var User $user */
		$user = $this->em->getRepository(User::class)
		                 ->findOneBy(['email' => $email]);

		if ($user == null) {
			throw new Security\AuthenticationException('Takový uživatel neexistuje');
		}

		if (!Security\Passwords::verify($password, $user->password)) {
			throw new Security\AuthenticationException('Špatné heslo');
		}

		if ($user->role->id !== Role::ADMIN) {
			throw new Security\AuthenticationException('Pokračovat dál může pouze uživatel s rozšířeným oprávněním.. Kontaktuj správce (Kubíka)');
		}

		return $user;

	}

}