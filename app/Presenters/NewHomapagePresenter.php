<?php

namespace App\Presenters;


final class NewHomepagePresenter extends BasePresenter
{
    public function checkRequirements($element): void
    {
        $this->getUser()->getStorage()->setNamespace('backend');
        parent::checkRequirements($element);
    }
}
