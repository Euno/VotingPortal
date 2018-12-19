<?php
use Phalcon\Mvc\View;
use Phalcon\Mvc\Controller;

class VoteController extends Controller
{
    public function indexAction($url = '')
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

        $voting = Votings::findFirst([
            'url = "'.addslashes($url).'" AND start_date <= '.time().' AND end_date >= '.time()
        ]);

        if(!$voting)
            exit('No voting found... Please try again later!');

        $this->view->voting = $voting;
        $this->view->answers = $voting->getAnswers();

        $used_addresses = $voting->getVotes([
            'confirmed IN (0, 1)',
        ]);

        $used_addresses_flat = [];
        foreach ($used_addresses as $ua)
        {
            if(!in_array($ua->masternode_address, $used_addresses_flat))
                $used_addresses_flat[] = $ua->masternode_address;
        }

        $nodes = file_get_contents('https://explorer.euno.co/api/getmasternodes');

        if($nodes)
        {
            $nodes = json_decode($nodes, true);

            foreach ($nodes as $ip => $address)
            {
                if(in_array($address, $used_addresses_flat))
                {
                    unset($nodes[$ip]);
                }
            }
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

    public function signedMsgCheckAction()
    {
        $this->view->disable();

        if( $this->request->isPost() )
        {
            $post = $this->request->getPost();

            $connect_string = sprintf('http://%s:%s@%s:%s/', $this->config->eunod->user, $this->config->eunod->pass, $this->config->eunod->host, $this->config->eunod->port);
            $coind = new jsonRPCClient($connect_string);

            $resultd = $coind->verifymessage($post['address'], $post['signedMessage'], $post['answer']);

            $result = false;
            switch(true)
            {
                case ($resultd === true):
                    $result = true;
                    break;

                case ($resultd === false):
                    $result = false;
                    break;

                case ($resultd === "error"):
                    $result = 'NO_CONNECTION';
                    break;
            }

            echo json_encode([
                "status" => $result
            ]);
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