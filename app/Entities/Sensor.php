<?php


namespace App\Entities;

use Kdyby\Doctrine\Entities\MagicAccessors;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 *
 * @property int    $id
 * @property string $name
 * @property string $description
 * @property string $uuid
 * @property bool   $active
 * @property bool   $activeOnHomepage
 * @property int    $ordering
 */
class Sensor {

	use MagicAccessors;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	protected $name;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	protected $description;

	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	protected $uuid;

	/**
	 * @ORM\Column(type="boolean", nullable=false, name="active")
	 * @var bool
	 */
	protected $active;

	/**
	 * @ORM\Column(type="boolean", nullable=false, name="active_on_homepage")
	 * @var bool
	 */
	protected $activeOnHomepage;

	/**
	 * @ORM\Column(type="integer", nullable=false, name="ordering")
	 * @var integer
	 */
	protected $ordering;

}