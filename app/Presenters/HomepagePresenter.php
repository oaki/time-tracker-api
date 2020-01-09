<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


final class HomepagePresenter extends BasePresenter
{
    /** @persistent */
    public $year;

    /** @persistent */
    public $month;


    public function getService($name)
    {
        return $this->context->getService($name);
    }

    function actionDelete($id)
    {
        $userModel = $this->getService('UserModel');

        $this->template->users = $userModel->delete($id);

        $this->redirect('Homepage:default');
    }

    function renderDefault()
    {
        $userModel = $this->getService('UserModel');

        $this->template->users = $userModel->fetchAll();
    }

    function renderView($userId)
    {
        $userModel = $this->getService('UserModel');


        $this->template->user = $userModel->fetch($userId);

        $year = $this->year ? $this->year : date('Y');
        $month = $this->month ? $this->month : date('n');
        $this->template->logList = $userModel->filterLog($userId, $year, $month);
    }

    function renderNewView($userId)
    {
        $userModel = $this->getService('UserModel');


        $this->template->user = $userModel->fetch($userId);

        $year = $this->year ? $this->year : date('Y');
        $month = $this->month ? $this->month : date('n');

        $this->template->logList = $userModel->filterNewLog($userId, $year, $month);
    }

    protected function createComponentAddUserForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form;
        $form->addText('name', 'Meno:')->isRequired();
        $form->addPassword('password', 'Heslo:')->isRequired();
        $form->addSubmit('add', 'Pridať');
        $form->onSuccess[] = [$this, 'registrationFormSucceeded'];
        return $form;
    }

    public function registrationFormSucceeded(Nette\Application\UI\Form $form, \stdClass $values): void
    {
        $this->flashMessage('Roboš bol úspešne pridaný.');

        $userModel = $this->getService('UserModel');
        $userModel->add($form->getValues());
        $this->redirect('Homepage:');
    }

    protected function createComponentSelectDateForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form;
        $years = [];
        for ($i = 0; $i < 10; $i++) {
            $year = 2019 + $i;
            $years[$year] = $year;
        }

        $month = [1 => "Január", 2 => "Február", 3 => "Marec", 4 => "Apríl", 5 => "Máj", 6 => "Jún", 7 => "Júl",
            8 => "August", 9 => "September", 10 => "Október", 11 => "November", 12 => "December"];

        $form->addSelect('year', '', $years)->isRequired();
        $form->addSelect('month', '', $month)->isRequired();


        $form->addSubmit('submit', 'Zobraz');
        $form->onSuccess[] = [$this, 'filterFormSucceeded'];

        $form->setDefaults([
            'year' => $this->year ? $this->year : date('Y'),
            'month' => $this->month ? $this->month : date('n')
        ]);
        return $form;
    }

    public function filterFormSucceeded(Nette\Application\UI\Form $form, \stdClass $values): void
    {
        $values = (array)$form->getValues();
        $values += ['q' => null];
        if ($this->isAjax()) {
            $this->year = $values['year'];
            $this->month = $values['month'];
            $this->redrawControl();
        } else {
            $this->redirect('this', $values);
        }
    }

    public function actionLogout(){
        $this->getUser()->logout();
        $this->redirect('default');
    }
}