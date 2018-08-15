<?php
namespace Backoffice\User\Controllers;

use System\Library\User\General as GeneralUser;

class IndexController extends \Backoffice\Controllers\BaseController
{

    public function indexAction()
    {
        $view = $this->view;

        $userLibrary = new GeneralUser();

        $view->user = $userLibrary->check();

        \Phalcon\Tag::setTitle("Manage Player - ".$this->_website->title);
    }
}
