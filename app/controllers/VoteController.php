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
    }

    public function doVoteAction($url = '')
    {
        $this->view->disable();

        if($this->request->isPost())
        {
            $post = $this->request->getPost();

            //print '<pre>';
            //print_r($post);

            $votes = [];

            foreach ($post['masternode_ipaddress_port'] as $k => $masternode_ipaddress_port)
            {
                $votes[] = [
                    'masternode_ipaddress_port' => $masternode_ipaddress_port,
                    'masternode_address' => $post['masternode_address'][$k],
                    'signed_msg' => $post['signed_msg'][$k],
                ];
            }

            $voting = Votings::findFirst('url = "'.addslashes($url).'"');

            foreach ($votes as $vote)
            {
                $voteModel = new Votes();
                $voteModel->voting_id = $voting->id;
                $voteModel->answer = $post['answer'];
                $voteModel->masternode_ipaddress_port = $vote['masternode_ipaddress_port'];
                $voteModel->masternode_address = $vote['masternode_address'];
                $voteModel->signed_msg = $vote['signed_msg'];
                $voteModel->date = time();
                $voteModel->confirmed = 0;
                $voteModel->create();
            }

            return $this->response->redirect('vote/thankyou/'.$url);
        }
    }

    public function notFoundAction()
    {
        http_response_code(404);
    }

    public function thankyouAction($url = '')
    {

    }
}