<?php
use Phalcon\Mvc\View;
use Phalcon\Mvc\Controller;

class VoteController extends Controller
{
    public function indexAction($url = '')
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

        $voting = Votings::findFirst('url = "'.addslashes($url).'"');

        $this->view->voting = $voting;
        $this->view->answers = $voting->getAnswers();

        $nodes = file_get_contents('https://explorer.euno.co/api/getmasternodes');

        if($nodes)
        {
            $nodes = json_decode($nodes, true);
        }

        $this->view->nodes = $nodes;

        //print '<pre>';
        //print_r($nodes);
        //exit;
    }

    public function doVoteAction($url = '')
    {

    }

    public function notFoundAction()
    {
        http_response_code(404);
    }
}