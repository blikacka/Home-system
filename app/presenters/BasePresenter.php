<?php

namespace App\Presenters;

use App\Entities\Role;
use App\Services\Security\Authenticator;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\MethodReflection;
use Nette\Application\UI\Presenter;
use Nette\Reflection\ClassType;


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
			if ($this->cronHash !== 'cb7ddd82ced9a4e1afd7abcf13cd8b862475bf55cc8feba0bf95d1fc03bdc536' && $this->name !== 'Temperature') {
				if ($this->name !== 'Sign' && ($this->action !== 'in' || $this->action !== 'register')) {
					$this->redirect('Sign:in');
				}
			}
		}
	}

	public function beforeRender() {
		parent::beforeRender();
		$this->template->userRole = $this->user->isLoggedIn() ? ($this->user->identity->role->id ?? null) : null;
		foreach (Role::getReflection()
		             ->getConstants() as $keyRole => $roleId) {
			$this->template->{'role' . $keyRole} = $roleId;
		}
	}

	/**
	 * Available methods for functions used in annotations. USED ONLY FOR FUNCTIONS
	 * ** @Roles(VISITOR, SUPER_ADMIN)          - Role names from \App\Entities\Role || Syntax: Role1, Role2, ... || Default (if role is not exist): Role::User
	 * ** @NotRoleRedirect(Homepage:default)    - Redirect to if user roles are not in list of roles. Used only if @Roles is filled || Syntax: same as Nette links
	 * ** @Redirect(Homepage:default)           - Instant redirect to defined page. Ignore other annotations || Syntax: same as Nette links
	 *
	 *
	 * @param $element
	 */
	public function checkRequirements($element) {
		parent::checkRequirements($element);
		if ($element instanceof MethodReflection && $this->user->isLoggedIn()) {
			$reflection = new ClassType($element->class);
			$annotations = $reflection->getMethod($element->name);

			/** Instant redirect */
			if ($annotations->hasAnnotation('Redirect')) {
				$this->redirect($annotations->getAnnotation('Redirect'));
			}

			/** Roles rules */
			if ($annotations->hasAnnotation('Roles')) {
				$annotationsRoles = $annotations->getAnnotation('Roles');
				$rolesReflectionConstants = Role::getReflection()
				                                ->getConstants();

				$roles = array_unique(array_map(function($role) use ($rolesReflectionConstants) {
					return $rolesReflectionConstants[$role] ?? Role::USER;
				}, (array)$annotationsRoles));

				$userHaveRole = false;

				array_map(function($userRole) use ($roles, &$userHaveRole) {
					if (in_array($userRole, $roles)) {
						$userHaveRole = true;
					}
				}, is_array($this->user->roles) ? array_map(function($role) {
					return $role->id;
				}, $this->user->roles) : [$this->user->roles->id]);

				if ($annotations->hasAnnotation('NotRoleRedirect') && !$userHaveRole) {
					$this->redirect($annotations->getAnnotation('NotRoleRedirect'));
				}

				if (!$userHaveRole) {
					$this->redirect('Homepage:default');
				}
			}
		}
	}

}
