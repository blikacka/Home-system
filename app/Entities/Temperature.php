<?php


namespace App\Entities;

use Kdyby\Doctrine\Entities\MagicAccessors;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 *
 * @property int       $id
 * @property string    $temperature
 * @property \DateTime $created
 * @property Sensor    $sensor
 */
class Temperature {

	use MagicAccessors;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 * @var string
	 */
	protected $temperature;

	/**
	 * @ORM\Column(type="datetime", name="created", nullable=false)
	 * @var \DateTime
	 */
	protected $created;

	/**
	 * @ORM\ManyToOne(targetEntity="Sensor")
	 * @ORM\JoinColumn(nullable=false)
	 * @var Sensor
	 */
	protected $sensor;

	/**
	 * Temperature constructor.
	 */
	public function __construct() {
		$this->created = new \DateTime();
	}


}