<?php
use Phalcon\Mvc\View;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $votings = Votings::find();

        $this->view->votings = $votings;
    }
}