<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Security\IIdentity;

/**
 * @ORM\Entity
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 * @property int    $id
 * @property string $email
 * @property string $password
 * @property string $name
 * @property Role   $role
 */
class User implements IIdentity {

	use MagicAccessors;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=300, unique=true, nullable=false)
	 */
	protected $email;

	/**
	 * @ORM\Column(type="string", length=300, nullable=false)
	 */
	protected $password;

	/**
	 * @ORM\Column(type="string", length=300, nullable=false)
	 */
	protected $name;

	/**
	 * @ORM\ManyToOne(targetEntity="Role")
	 * @ORM\JoinColumn(nullable=false)
	 * @var Role
	 */
	protected $role;

	public function getId() {
		return $this->id;
	}

	public function getRoles() {
		return $this->role;
	}
}