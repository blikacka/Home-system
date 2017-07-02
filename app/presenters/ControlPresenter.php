<?php


namespace App\Presenters;

use Nette\Application\UI\Form;


/**
 * @author Jakub Cieciala <jakub.cieciala@gmail.com>
 */
class ControlPresenter extends BasePresenter {

	/**
	 * @Roles(ADMIN)
	 * @NotRoleRedirect(Homepage:default)
	 */
	public function actionDefault() {
		parent::startup();
	}

	/**
	 * Control buttons
	 * @return Form
	 */
	public function createComponentControlButtons() {
		$form = new Form();
		$form->addGroup('Zapínání');
		$form->addSubmit('zap16', 'Zapnout podlahovku')
		     ->setAttribute('class', 'btn btn-default btn-block');
		$form->addSubmit('zap19', 'Zapnout elektrokotel')
		     ->setAttribute('class', 'btn btn-default btn-block');
		$form->addSubmit('zap20', 'Zapnout obehove cerpadlo')
		     ->setAttribute('class', 'btn btn-default btn-block');

		$form->addGroup('Vypínání');
		$form->addSubmit('vyp16', 'Vypnout podlahovku')
		     ->setAttribute('class', 'btn btn-default btn-block');
		$form->addSubmit('vyp19', 'Vypnout elektrokotel')
		     ->setAttribute('class', 'btn btn-default btn-block');
		$form->addSubmit('vyp20', 'Vypnout obehove cerpadlo')
		     ->setAttribute('class', 'btn btn-default btn-block');

		$form->addGroup('Stav');
		$form->addSubmit('status16', 'Stav podlahovka')
		     ->setAttribute('class', 'btn btn-default btn-block');
		$form->addSubmit('status19', 'Stav elektrokotel')
		     ->setAttribute('class', 'btn btn-default btn-block');
		$form->addSubmit('status20', 'Stav obehove cerpadlo')
		     ->setAttribute('class', 'btn btn-default btn-block');

		$form->onSubmit[] = function($form) {
			$touchedButton = array_keys($form->getForm()
			                                 ->getHttpData())[0];
			$out = shell_exec(escapeshellcmd('sudo python /var/www/html/' . $touchedButton . '.py'));
			$this->flashMessage($out);
			$this->redirect('this');
		};

		return $form;
	}

}