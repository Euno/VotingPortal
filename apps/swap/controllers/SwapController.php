<?php
namespace EunoVoting\Swap\Controllers;

use EunoVoting\Common\Libraries\jsonRPCClient;
use EunoVoting\Common\Models\SwapRequests;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Controller;

class SwapController extends Controller
{
    public function initialize()
    {
        exit('Swap has ended on 11/12/2020 11:30AM UTC');
    }
    
    public function indexAction()
    {
        $uri = $_SERVER['REQUEST_URI'];

        if($uri === '/governance')
        {
            $open = 1602525600;
        }
        else
        {
            $open = 1602529200;
        }

        if(
            getenv('ENVIRONMENT') === 'production' &&
            time() < $open
        )
            exit('Swap form will be open on the 12th of October at 19:00 GMT!');

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function doSwapAction()
    {
        $this->view->disable();

        if($this->request->isPost())
        {
            $post = $this->request->getPost();

            $connect_string = sprintf(
                'http://%s:%s@%s:%s/',
                getenv('SWAPRPCUSER'),
                getenv('SWAPRPCPASS'),
                getenv('SWAPRPCHOST'),
                getenv('SWAPRPCPORT')
            );
            $coind = new jsonRPCClient($connect_string);

            $resultd = $coind->getnewaddress('new address: '.$post['new_address']);

            if($resultd !== 'error')
            {
                $random = new \Phalcon\Security\Random();

                $request = new SwapRequests();
                $request->uuid = $random->uuid();
                $request->new_address = $post['new_address'];
                $request->immediate_address = $resultd;
                $request->notify_address = $post['notify_address'];
                $request->create();

                $this->response->redirect('swap/result/'.$request->uuid);
            }
        }
    }

    public function resultAction( $uuid = '' )
    {
        if(!$uuid)
            $this->response->redirect('swap');

        $swap = SwapRequests::findFirst([
            'uuid = :uuid:',
            'bind' => [
                'uuid' => $uuid
            ]
        ]);

        if(!$swap)
            $this->response->redirect('swap');

        //$balance = file_get_contents("https://explorerold.euno.co/ext/getbalance/".$swap->immediate_address);

        //$this->view->balance = ctype_digit($balance) ? $balance : json_decode($balance);
        $this->view->swap = $swap;
        $this->view->link = $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";;
    }
}
