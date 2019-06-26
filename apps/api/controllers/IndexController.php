<?php
namespace EunoVoting\Api\Controllers;

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function initialize()
    {
        $this->view->disable();
    }

    public function indexAction()
    {
        echo 'EUNO governance API';
    }
}
