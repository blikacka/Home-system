<?php


namespace App\Forms;
use App\Entities\Sensor;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;
use Nette\SmartObject;


/**
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 */

class SensorForm {

	use SmartObject;

	/** @var FormFactory */
	public $formFactory;

	/** @var EntityManager */
	public $em;

	/**
	 * SensorForm constructor.
	 * @param $formFactory
	 * @param EntityManager $em
	 */
	public function __construct(FormFactory $formFactory, EntityManager $em) {
		$this->formFactory = $formFactory;
		$this->em = $em;
	}

	public function create(callable $onSuccess, Sensor $sensor = null) {
		$form = $this->formFactory->create();
		$form->addText('name', 'Název')
		     ->setRequired('Zadejte název čidla');
		$form->addTextArea('description', 'Popis');

		$form->addText('uuid', 'ID čidla')
		     ->setRequired('Zadejte ID čidla');

		if ($sensor !== null) {
			$form->setDefaults([
				'name' => $sensor->name,
				'description' => $sensor->description,
				'uuid' => $sensor->uuid
			]);
		}

		$form->addSubmit('send', $sensor == null ? 'Přidat' : 'Upravit');

		$form->onSuccess[] = function(Form $form, $values) use ($onSuccess, $sensor) {
			if ($sensor === null) {
				$sensor = new Sensor();
			} else {
				$sensor = $this->em->find(Sensor::class, $sensor);
			}
			$sensor->name = $values->name;
			$sensor->description = $values->description;
			$sensor->uuid = $values->uuid;
			$sensor->active = true;
			$this->em->persist($sensor);
			$this->em->flush();
			$onSuccess();
		};

		return $form;
	}


}