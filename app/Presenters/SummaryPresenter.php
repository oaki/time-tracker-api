<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\LogModel;
use App\Model\SummaryModel;
use Nette;
use Tracy\Debugger;


final class SummaryPresenter extends BasePresenter
{
    /** @persistent */
    public $year;


    function renderDefault()
    {
        $year = $this->year ?? date('Y');
        $this->template->year = $year;

        $userModel = $this->getService('UserModel');
        $summaryModel = $this->getService('SummaryModel');


        $this->template->months = SummaryModel::getMonths($year);
        $this->template->users = $userModel->fetchAll();


        $this->template->summary = $summaryModel->fetchAllByYear($year);

        $this->template->getWeekDay = function ($y, $m, $d) {
            $fullDate = $y . '-' . $m . '-' . $d;
            $dayName = date('D', strtotime($fullDate));
            return SummaryModel::translateWeekDay($dayName);
        };


//        var_dump($this->template->summary);
//        exit;
    }

    function actionSave()
    {
        $post = $this->getHttpRequest()->getPost();     // array of all POST parameters

        $data = json_decode($post['json']);
        $summaryId = $data->summaryId;
        $type = $data->type;
        $value = $data->value;
        $userId = intval($data->userId);

        $summaryModel = $this->getService('SummaryModel');
        $jsonData = $summaryModel->fetchJson($summaryId);

        $obj = json_decode($jsonData);


        $jsonToSave = '';

        foreach ($obj as $row) {
            if ($row->userId === $userId) {
                switch ($type) {
                    case 'hour':
                        $days = $row->days;
                        $day = intval($data->day);

                        foreach ($days as $item) {
                            if (intval($item->day) === intval($day)) {
                                $item->sum = floatval($value);
                            }
                        }
                        $jsonToSave = json_encode($obj);
                        break;
                    case 'user-rate':
                        $row->userRate = floatval($value);
                        $jsonToSave = json_encode($obj);
                        break;

                    case 'user-payout':
                        $row->userPayout = floatval($value);
                        $jsonToSave = json_encode($obj);
                        break;

                    case 'user-pay-extra':
                        $row->userPayExtra = floatval($value);
                        $jsonToSave = json_encode($obj);
                        break;
                }
            }
        }

        if ($jsonToSave) {
            $summaryModel->update($summaryId, $jsonToSave);
        }

        exit;
    }

    function actionGenerate($year, $month)
    {
        $userModel = $this->getService('UserModel');
        $summaryModel = $this->getService('SummaryModel');

        $users = $userModel->fetchAll();
        $days = LogModel::monthDays($year, $month);
        $numOfDays = count($days);

        $jsonObj = [];
        foreach ($users as $user) {
            $logList = $userModel->filterNewLog($user['id'], $year, $month);
            $totalSum = 0;
            $days = [];
            for ($i = 1; $i <= $numOfDays; $i++) {
                $fullDate = $year . '-' . $month . '-' . $i;

                $arr = [
                    'day' => $i,
                    'fullDate' => $fullDate,
                    'sum' => $logList[$i]['sum'] ?? 0,

                ];

                $days[] = $arr;

                $totalSum += $arr['sum'];
            }

            $obj = [
                'total' => $totalSum,
                'month' => $month,
                'userId' => $user['id'],
                'userName' => $user['name'],
                'userRate' => $user['rate'],
                'userPayout' => 0,
                'userPayExtra' => 0,
                'days' => $days
            ];

            $jsonObj[] = $obj;
        }

        $summaryModel->insertOrUpdate($year, $month, json_encode($jsonObj));
        $this->flashMessage('Dochádzka za ' . $month . '. mesiac bola vygenerovaná.');

        $this->redirect('default');
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

        $form->addSelect('year', 'Vyber rok:', $years)->isRequired();

        $form->addSubmit('submit', 'Zobraz');
        $form->onSuccess[] = [$this, 'filterFormSucceeded'];

        $form->setDefaults([
            'year' => $this->year ? $this->year : date('Y'),
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
}