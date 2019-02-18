<?php
namespace EunoVoting\Signup\Controllers;

use EunoVoting\Common\Libraries\jsonRPCClient;
use EunoVoting\Common\Models\Votings;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Controller;

class SignupController extends Controller
{
    public function indexAction($url = '')
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

        $used_addresses = [];

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
}