<?php


namespace App\Presenters;

use App\Entities\Sensor;
use App\Entities\Temperature;
use App\Forms\SensorForm;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use App\Services\Libraries\LayoutHor;
use App\Services\Libraries\LayoutVert;
use App\Services\Libraries\OdoGraph;
use App\Services\Libraries\Odometer;
use Nette\Application\Responses\JsonResponse;
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

	public function getTempData($selectedDates = true) {
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
				if ($selectedDates) {
					if (($index % $maxResults) == 0) {
						$tempDates[] = $temperature->created->format("d.m.Y H:i");
					} else {
						$tempDates[] = "";
					}
				} else {
					$tempDates[] = $temperature->created->format("d.m.Y H:i");
				}

				$index++;
			}
		}

		return [
			'sensor' => $sensor,
			'tempData' => $tempData,
			'tempDates' => $tempDates
		];

	}

	public function renderTest() {
		$tmp = $this->getTempData(false);
		$result = [];

		foreach ($tmp['tempData'] as $key => $item) {
			$tempDate = (new \DateTime($tmp['tempDates'][$key]))->format('D M d Y H:i:s O');
			$result[] = [$tempDate, (float)$item];
		}

		$this->template->testData = $result;
	}

	public function handleGetTempData() {
		$tmp = $this->getTempData(false);
		$result = [];

		foreach ($tmp['tempData'] as $key => $item) {
			$tempDate = (new \DateTime($tmp['tempDates'][$key]))->format('D M d Y H:i:s O');
			$result[] = [$tempDate, (float)$item];
		}

		$this->sendResponse(new JsonResponse($result));


	}

	public function handleGetLastTemp() {

	}

	public function renderGraph() {
		if ($this->uuid === null) {
			return;
		}

		$tmp = $this->getTempData();
		$tempData = $tmp['tempData'] ?? [];
		$sensor = $tmp['sensor'] ?? [];
		$tempDates = $tmp['tempDates'] ?? [];

		if (count($tempData) > 0) {
			$graph = new Graph\Graph(2500, 500);
			$graph->SetScale("textlin");
			$graph->SetY2Scale("lin");
			$graph->SetShadow();


			// Create the two linear plot
			$lineplot = new Plot\LinePlot($tempData);
			//		$lineplot->SetStepStyle();
			$lineplot->mark->SetType(MARK_DIAMOND);

			// Add the plot to the graph
			$graph->Add($lineplot);
			$graph->AddY2($lineplot);

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

	/**
	 * Form for filter
	 * @return Form
	 */
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

		$form->addSelect('interval', 'Změnit interval v grafu', $dataVal);
		$form->addSubmit('submit', 'Změnit');

		$form['submit']->onClick[] = function($form) {
			$values = $form->getForm()
			               ->getValues();
			$this->redirect('this', ['dateInterval' => $values['interval']]);
		};

		return $form;
	}

	public function renderLastOdo() {
		//
		$sensors = $this->em->createQueryBuilder()
		                    ->select('s')
		                    ->from(Sensor::class, 's')
		                    ->where('s.active = :true')
		                    ->setParameter('true', true)
		                    ->getQuery()
		                    ->getResult();

		$graph = new OdoGraph(250, count($sensors) * 128);

		$odo = [];

		/**
		 * @var        $i
		 * @var Sensor $sensor
		 */
		foreach ($sensors as $i => $sensor) {
			/** @var Temperature $temp */
			$temp = $this->em->createQueryBuilder()
			                 ->select('t')
			                 ->from(Temperature::class, 't')
			                 ->where('t.sensor = :sensor')
			                 ->setParameter('sensor', $sensor)
			                 ->andWhere('t.temperature != :empty')
			                 ->setParameter('empty', '')
			                 ->andWhere('t.temperature IS NOT NULL')
			                 ->addOrderBy('t.created', 'DESC')
			                 ->setMaxResults(1)
			                 ->getQuery()
			                 ->getSingleResult();
			$odo[$i] = new Odometer();
			$odo[$i]->SetColor("lightyellow");
			$odo[$i]->needle->Set($temp->temperature);
			$odo[$i]->needle->SetStyle(NEEDLE_STYLE_SMALL_TRIANGLE, NEEDLE_ARROW_SL);
			$odo[$i]->caption->SetFont(FF_VERDANA, FS_NORMAL, 6);
			$odo[$i]->caption->Set($sensor->name . ' | ' . $temp->created->format('d.m.Y H:i:s') . ' | ' . $temp->temperature . ' °C');
			$odo[$i]->AddIndication(-20, 0, "blue:0.4");
			$odo[$i]->AddIndication(0, 20, "blue:0.7");
			$odo[$i]->AddIndication(20, 40, "blue:1.4");
			$odo[$i]->AddIndication(40, 77, "green:0.9");
			$odo[$i]->AddIndication(77, 90, "yellow");
			$odo[$i]->AddIndication(90, 100, "red");
			$odo[$i]->AddIndication(100, 110, "red:0.8");

			$odo[$i]->SetCenterAreaWidth(0.25);
			$odo[$i]->scale->Set(-20, 110);
			$odo[$i]->scale->SetTicks(5, 2);
			$odo[$i]->needle->SetLength(0.7);
		}

		$rows = [];
		foreach ($odo as $item) {
			$rows[] = new LayoutHor([
				$item
			]);
		}

		$col1 = new LayoutVert($rows);

		$graph->Add($col1);
		$graph->Stroke();
	}


}