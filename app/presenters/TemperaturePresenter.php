<?php


namespace App\Presenters;

use App\Entities\Sensor;
use App\Entities\Temperature;
use Nette\Application\Responses\JsonResponse;


/**
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 */
class TemperaturePresenter extends BasePresenter {

	/** @persistent */
	public $sensor;

	/** @persistent */
	public $temperature;

	/**
	 * Save temperature with values from url
	 * URL parameters -
	 * ** sensor - uuid of sensor
	 * ** temperature
	 * **** URL - /temperature/save-temp?sensor=Sdad&temperature=22
	 */
	public function actionSaveTemp() {
		$sensor = $this->sensor === null ? null : $this->em->getRepository(Sensor::class)
		                                                   ->findOneBy(['uuid' => $this->sensor]);
		if ($sensor === null) {
			$this->flashMessage('Takový senzor neexistuje...');
			$this->redirect('Homepage:');
		}

		if ($this->temperature === null) {
			$this->flashMessage('Není zadaná teplota...');
			$this->redirect('Homepage:');
		}

		$temperature = new Temperature();
		$temperature->temperature = $this->temperature;
		$temperature->sensor = $sensor;
		$this->em->persist($temperature);
		$this->em->flush();
		$this->sendResponse(new JsonResponse(['status' => 'ok']));
		$this->terminate();
	}

	/**
	 * Save temperatures from file temp.txt placed inside www folder
	 * Syntax of file: sensor_uuid=temperature|sensor_uuid2=temperature2
	 * URL - /temperature/save-temp-from-file
	 */
	public function actionSaveTempFromFile() {

		$file = file_get_contents('./temp.txt', true);

		$sensorValues = explode('|', preg_replace('/\s+/', '', $file));

		foreach ($sensorValues as $sensorValue) {
			$sensorValueExploded = explode('=', $sensorValue);
			$sensorUuid = $sensorValueExploded[0] ?? null;
			$sensorTemp = $sensorValueExploded[1] ?? null;

			$sensor = $sensorUuid === null ? null : $this->em->getRepository(Sensor::class)
			                                                   ->findOneBy(['uuid' => $sensorUuid]);

			if ($sensor === null) {
				$this->flashMessage('Takový senzor neexistuje...');
				$this->redirect('Homepage:');
			}

			if ($sensorTemp === null) {
				$this->flashMessage('Není zadaná teplota...');
				$this->redirect('Homepage:');
			}

			$temperature = new Temperature();
			$temperature->temperature = $sensorTemp;
			$temperature->sensor = $sensor;

			$this->em->persist($temperature);

		}
		$this->em->flush();

		$this->sendResponse(new JsonResponse(['status' => 'ok']));
		$this->terminate();
	}

	/**
	 * Save temperatures from system files
	 * URL - /temperature/read-and-save-temp-from-system
	 */
	public function actionReadAndSaveTempFromSystem() {
		$sensors = $this->em->getRepository(Sensor::class)
		                    ->findBy(['active' => true]);

		foreach ($sensors as $sensor) {
			$sensorPath = '/mnt/1wire/'. $sensor->uuid .'/temperature';
			$tempSensorRawData = @implode('', @file($sensorPath));
			if ($tempSensorRawData !== null && $tempSensorRawData !== '') {
				$temperature = new Temperature();
				$temperature->temperature = $tempSensorRawData;
				$temperature->sensor = $sensor;

				$this->em->persist($temperature);
			}
		}

		$this->em->flush();
		$this->sendResponse(new JsonResponse(['status' => 'ok']));
		$this->terminate();
	}

	/**
	 * Save temperature from file specified by logtemp
	 * URL - /temperature/save-temp-from-logtemp
	 */
	public function actionSaveTempFromLogtemp() {
		$csv = @array_map('str_getcsv', @file('./last.csv'));

		if ($csv !== null) {
			foreach ($csv as $data) {
				$sensorUuid = $data[1] ?? null;
				$sensor = $sensorUuid === null ? null : $this->em->getRepository(Sensor::class)
				                                                 ->findOneBy(['uuid' => $sensorUuid]);

				if ($sensor !== null) {
					$temperature = new Temperature();
					$temperature->temperature = $data[2] ?? null;
					$temperature->sensor = $sensor;
					$this->em->persist($temperature);
				}
			}
		}

		$this->em->flush();
		$this->sendResponse(new JsonResponse(['status' => 'ok']));
		$this->terminate();
	}

}