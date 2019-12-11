<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    function startup(){
        parent::startup();

        if (isset($_GET['APPLICATION_API_KEY'])) {
            // pass cdf4e4a544ed397ed44eee59691ee759a8a82a85
            $this->user->login('admin', $_GET['APPLICATION_API_KEY']);
        }

        if (!$this->user->isLoggedIn()) {

            throw new Nette\Application\ForbiddenRequestException;
        }
    }
}