<?php

namespace App\Presenters;


use Nette\Application\UI\Form;

final class UserPresenter extends BasePresenter
{


    function renderDefault($id)
    {
        $model = $this->getService('UserModel');

        $user = $model->fetch($id);

        $this['editUserForm']->setDefaults([
            'name'=>$user->name,
            'password'=>$user->password,
            'rate'=>$user->rate,
        ]);

        $this->template->user = $user;

    }

    protected function createComponentEditUserForm(){
        $form = new Form();

        $form->addText('name', 'Meno:')->isRequired();
        $form->addText('password', 'Heslo:')->isRequired();
        $form->addText('rate', 'Hodinova sadzba:')->isRequired();
        $form->addSubmit('save', 'Upraviť');
        $form->onSuccess[] = [$this, 'formSucceeded'];

        $form->setDefaults([
            'name' => 'John',
            'age' => '33'
        ]);

        return $form;
    }

    function formSucceeded(Form $form){
        $userId = $this->presenter->getParameter('id');
        $model = $this->getService('UserModel');
        $values = $form->getValues();

        $model->update($values, $userId);

        $this->flashMessage($values['name'] . ' bol uložený.');
        $this->redirect('Homepage:view', ['userId'=>$userId]);
        
    }
}

