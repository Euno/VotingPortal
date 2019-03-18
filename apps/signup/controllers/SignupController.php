<?php
namespace EunoVoting\Signup\Controllers;

use EunoVoting\Common\Libraries\jsonRPCClient;
use EunoVoting\Common\Models\GovernanceMembers;
use EunoVoting\Common\Models\Votings;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Controller;

class SignupController extends Controller
{
    public function indexAction($url = '')
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

        $connect_string = sprintf('http://%s:%s@%s:%s/', $this->config->eunod->user, $this->config->eunod->pass, $this->config->eunod->host, $this->config->eunod->port);
        $coind = new jsonRPCClient($connect_string);

        $resultd = $coind->masternode('list', 'pubkey');

        if($resultd !== false && $resultd !== 'error')
        {
            $nodes = $resultd;
        }
        else
        {
            $nodes = file_get_contents('https://explorer.euno.co/api/getmasternodes');

            if(!$nodes)
            {
                $nodes = [];
            }
        }

        $this->view->nodes = $nodes;
    }

    public function saveAction()
    {
        $this->view->disable();

        if($this->request->isPost())
        {
            $post = $this->request->getPost();

            $signup = new GovernanceMembers();
            $signup->save($post);

            return $this->response->redirect('signup/thankyou');
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

            $resultd = $coind->verifymessage($post['address'], $post['signedMessage'], $post['telegram_username']);

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

    public function thankyouAction(){}
}