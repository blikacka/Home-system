<?php


namespace App\Entities;

use Kdyby\Doctrine\Entities\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 */
class Role extends BaseEntity {

	const ADMIN = 1;
	const USER = 2;
	const SPECIAL = 3;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/** @ORM\Column(type="integer", nullable=false) */
	protected $key;

	/** @ORM\Column(type="string", length=20, nullable=false) */
	protected $name;

}

/**
 * INSERT INTO `role` (`id`, `key`, `name`) VALUES
 * (1,    1,    'Admin'),
 * (2,    2,    'User');
 */