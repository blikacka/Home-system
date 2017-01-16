<?php


namespace App\Presenters;

use App\Entities\Sensor;
use App\Entities\Temperature;
use App\Forms\SensorForm;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use Nette\Application\UI\Form;
use Nette\Caching\Storages\DevNullStorage;
use Nette\DI\Container;
use Tester\Environment;


/**
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 */
class SensorPresenter extends BasePresenter {

	/** @persistent */
	public $uuid = null;

	/** @persistent */
	public $dateInterval = null;

	/** @var SensorForm @inject */
	public $sensorForm;

	public function renderDefault() {
		$this->template->sensors = $this->em->getRepository(Sensor::class)
		                                    ->findAll();
		$this->template->temperature = function($sensor) {
			$lastTemp = $this->em->getRepository(Temperature::class)
			                     ->findOneBy(['sensor' => $sensor], ['id' => 'DESC']);

			return [
				'value' => $lastTemp->temperature,
				'datetime' => ($lastTemp->created)->format('d.m.Y H:i:s')
			];
		};
	}

	/**
	 * @param bool $codedUuid
	 * @param null $dateLimit
	 * @return array
	 */
	public function getTemperatures($codedUuid = false, $dateLimit = null) {
		$query = $this->em->createQueryBuilder()
		                  ->select('t, s')
		                  ->from(Temperature::class, 't')
		                  ->leftJoin('t.sensor', 's')
		                  ->where('s.uuid = :sensor')
		                  ->setParameter('sensor', $codedUuid ? base64_decode($this->uuid) : $this->uuid);

		if ($dateLimit != null) {
			$query->andWhere('t.created >= :dateLimit')
			      ->setParameter('dateLimit', $dateLimit);
		}

		return $query->getQuery()
		             ->getResult();

	}

	/**
	 * @return null|string
	 */
	public function getDateInterval() {
		if ($this->dateInterval === null) {
			return 'P1D';
		}

		return $this->dateInterval;
	}

	public function renderGraph() {
		if ($this->uuid === null) {
			return;
		}

		$temperatures = $this->getTemperatures(true, (new \DateTime())->sub(new \DateInterval($this->getDateInterval())));
		$sensor = $this->em->getRepository(Sensor::class)
		                   ->findOneBy(['uuid' => base64_decode($this->uuid)]);

		$tempData = [];
		$tempDates = [];

		$index = 0;

		$sumTemperatures = count($temperatures);
		$maxResults = (int)ceil($sumTemperatures / 40);

		foreach ($temperatures as $temperature) {
			if ($temperature->temperature != null) {
				$tempData[] = $temperature->temperature;
				if (($index % $maxResults) == 0) {
					$tempDates[] = $temperature->created->format("d.m.Y H:i");
				} else {
					$tempDates[] = "";
				}
				$index++;
			}
		}

		if (count($tempData) > 0) {
			// Create the graph and specify the scale for both Y-axis
			$graph = new Graph\Graph(2500, 500);
			$graph->SetScale("textlin");
			$graph->SetShadow();

			// Adjust the margin
			$graph->img->SetMargin(40, 40, 20, 70);

			// Create the two linear plot
			$lineplot = new Plot\LinePlot($tempData);
			//		$lineplot->SetStepStyle();
			$lineplot->mark->SetType(MARK_SQUARE);

			// Adjust the axis color
			$graph->yaxis->SetColor("blue");

			$graph->title->Set($sensor->name);

			$graph->title->SetFont(FF_FONT1, FS_BOLD);
			$graph->xaxis->SetFont(FF_VERDANA, FS_NORMAL, 6);
			$graph->yaxis->SetFont(FF_VERDANA, FS_NORMAL, 8);

			$graph->xaxis->SetTickLabels($tempDates);
			$graph->xaxis->SetLabelAngle(50);
			$graph->xaxis->SetColor('darkblue', 'black');
			$graph->img->SetMargin(100, 60, 40, 110);


			// Set the colors for the plots
			$lineplot->SetColor("blue");
			$lineplot->SetWeight(2);


			// Add the plot to the graph
			$graph->Add($lineplot);

			// Adjust the legend position
			$graph->legend->SetLayout(LEGEND_HOR);
			$graph->legend->Pos(0.4, 0.95, "center", "bottom");

			// Display the graph
			$graph->Stroke();
		}
	}

	public function createComponentSensorForm() {
		if ($this->uuid !== null) {
			$sensor = $this->em->getRepository(Sensor::class)
			                   ->findOneBy(['uuid' => $this->uuid]);
		} else {
			$sensor = null;
		}

		return $this->sensorForm->create(function() use ($sensor) {
			$this->flashMessage($sensor === null ? 'Senzor by přidán' : 'Senzor byl upraven');
			$this->redirect('Sensor:');
		}, $sensor);

	}

	public function createComponentDateIntervalForm() {
		$form = new Form();

		$dataVal = [];

		for ($i = 10; $i < 60; $i += 10) {
			$dataVal['PT' . $i . 'M'] = $i . ' minut zpět';
		}

		for ($i = 1; $i < 24; $i++) {
			$dataVal['PT' . $i . 'H'] = $i . ' hodin zpět';
		}

		for ($i = 1; $i <= 365; $i++) {
			$dataVal['P' . $i . 'D'] = $i . ' dnů zpět';
		}

		$form->addSelect('interval', 'Interval v grafu', $dataVal);
		$form->addSubmit('submit', 'Potvrdit');

		$form['submit']->onClick[] = function($form) {
			$values = $form->getForm()->getValues();
			$this->redirect('this', ['dateInterval' => $values['interval']]);
		};

		return $form;
	}


}