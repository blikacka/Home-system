<?php


namespace App\Presenters;

use App\Entities\Sensor;
use App\Entities\Temperature;
use App\Forms\SensorForm;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use Nette\Caching\Storages\DevNullStorage;
use Nette\DI\Container;
use Tester\Environment;


/**
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 */
class SensorPresenter extends BasePresenter {

	/** @persistent */
	public $uuid = null;

	/** @var SensorForm @inject */
	public $sensorForm;

	public function renderDefault() {
		$this->template->sensors = $this->em->getRepository(Sensor::class)
		                                    ->findAll();
	}

	public function getTemperatures() {
		return $this->em->createQueryBuilder()
		                ->select('t, s')
		                ->from(Temperature::class, 't')
		                ->leftJoin('t.sensor', 's')
		                ->where('s.uuid = :sensor')
		                ->setParameter('sensor', $this->uuid)
		                ->getQuery()
		                ->getResult();
	}

	public function renderGraph() {
		if ($this->uuid != null) {
			$temperatures = $this->getTemperatures();
			$sensor = $this->em->getRepository(Sensor::class)
			                   ->findOneBy(['uuid' => $this->uuid]);

		} else {
			return;
		}

		$tempData = [];
		$tempDates = [];
		foreach ($temperatures as $temperature) {
			$tempData[] = $temperature->temperature;
			$tempDates[] = $temperature->created->format("d.m.Y H:i");
		}

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


}