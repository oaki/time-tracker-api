<?php


namespace App\Presenters;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\Checkbox;
use Ublaboo\DataGrid\DataGrid;




final class HolidaysPresenter extends BasePresenter
{
    /** @persistent */
    public $year;

    /** @persistent */
    public $month;


    function renderDefault()
    {
        $model = $this->getService('HolidayModel');

        $this->template->holidays = $model->fetchAll();
    }

    function renderAdd()
    {
        $model = $this->getService('HolidayModel');

        $this->template->holidays = $model->fetchAll();
    }

    function actionImport()
    {
        $model = $this->getService('HolidayModel');
        $model->fetchHolidays();
        exit;
    }


    public function createComponentSimpleGrid($name)
    {
        $model = $this->getService('HolidayModel');

        $grid = new DataGrid($this, $name);

        $grid->setDataSource($model->getFluent());
        $grid->addColumnDateTime('date', 'Datum')
            ->setFormat('Y-m-d')
            ->setSortable()
            ->setEditableCallback(function ($id, $value) use ($model): void {
                $model->update(['date' => $value], $id);
            });
        $grid->addColumnText('event', 'Udalosť')
            ->setEditableCallback(function ($id, $value) use ($model): void {
                $model->update(['event' => $value], $id);
            });

//        $grid->addInlineAdd()
//            ->onControlAdd[] = function(\Nette\Forms\Container $container) {
//            $container->addText('id', '')->setHtmlAttribute('readonly');
//            $container->addText('name', '');
//            $container->addText('inserted', '');
//            $container->addText('link', '');
//        };
//
//        $grid->getInlineAdd()->onSubmit[] = function(ArrayHash $values): void {
//            $v = '';
//
//            foreach($values as $key => $value) {
//                $v =$v."$key: $value, ";
//	}
//
//            $v = trim($v,', ');
//            dump($v);
//            exit;
//            $this->flashMessage("Record with values [$v] was added! (not really)", 'success');
//            $this->redrawControl('flashes');
//        };

        $grid->addAction('delete', '', 'delete!')
            ->setIcon('trash')
            ->setTitle('Zmazat')
            ->setClass('btn btn-xs btn-danger <strong class="text-danger">ajax</strong>');
    }

    function handleDelete($id)
    {
        $model = $this->getService('HolidayModel');
        $model->delete($id);
        $this->flashMessage("Zaznam vymazany", 'success');
        $this->redrawControl('flashes');

    }

    public function getService($name)
    {
        return $this->context->getService($name);
    }

    protected function createComponentAddHolidayForm()
    {

        $form = new Form();

        $form->addText('date', 'Datum:')->setHtmlAttribute('class', 'datepicker')->isRequired();
        $form->addText('event', 'Udalost:')->isRequired();
        $form->addSubmit('add', 'Pridať');
        $form->onSuccess[] = [$this, 'addHolidayFormSucceeded'];
        return $form;
    }

    public function addHolidayFormSucceeded(Form $form, \stdClass $values): void
    {
        $this->flashMessage('Roboš bol úspešne pridaný.');

        $model = $this->getService('HolidayModel');
        $model->insert($form->getValues());
        $this->redirect('Holidays:');
    }

}

