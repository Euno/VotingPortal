<?php
namespace EunoVoting\VotingFrontend\Controllers;

use EunoVoting\Common\Libraries\jsonRPCClient;
use EunoVoting\Common\Models\Votes;
use EunoVoting\Common\Models\Votings;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Controller;

class VoteController extends Controller
{
    public function indexAction($url = '')
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

        $voting = Votings::findFirst([
            'url = "'.addslashes($url).'" AND start_date <= '.time().' AND end_date >= '.(time()-(8*60*60)) //GMT+1 to GMT -7
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

            if(!isset($nodes['result']))
                exit('Error fetching the active nodes');

            foreach ($nodes['result'] as $k => $node)
            {
                if(in_array($node['addr'], $used_addresses_flat))
                {
                    unset($nodes[$k]);
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
                $voteHash = sha1($vote['masternode_ipaddress_port'].$vote['masternode_address']);

                if(Votes::findFirst('voting_id = '.$voting->id.' AND vote_hash = "'.$voteHash.'" AND confirmed IN (0, 1)'))
                    continue;

                $voteModel = new Votes();
                $voteModel->voting_id = $voting->id;
                $voteModel->answer = $post['answer'];
                $voteModel->masternode_ipaddress_port = $vote['masternode_ipaddress_port'];
                $voteModel->masternode_address = $vote['masternode_address'];
                $voteModel->signed_msg = $vote['signed_msg'];
                $voteModel->date = time();
                $voteModel->anon_vote = 0;
                $voteModel->vote_hash = $voteHash;
                $voteModel->confirmed = 0;
                $voteModel->create();

                if($voteModel->checkHash() !== 'error' && $voteModel->checkHash() !== false)
                {
                    $voteModel->confirmed = 1;
                    $voteModel->update();
                }
                else
                {
                    $voteModel->confirmed = 2;
                    $voteModel->update();
                }

                if(isset($post['vote_anon']) && $post['vote_anon'] == 1)
                {
                    $voteModel->anon_vote = 1;
                    $voteModel->masternode_address = '';
                    $voteModel->masternode_ipaddress_port = '';
                    $voteModel->update();
                }
            }

            return $this->response->redirect('thankyou/'.$url);
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

                case ($resultd === false || $resultd === "error"):
                    $result = false;
                    break;
            }

            echo json_encode([
                "status" => $result
            ]);
        }
    }

    public function thankyouAction($url = '')
    {
        $this->view->redirectUrl = $url;
    }
}
