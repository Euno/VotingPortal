<?php
namespace EunoVoting\Swap\Controllers;

use EunoVoting\Common\Libraries\jsonRPCClient;
use EunoVoting\Common\Models\SwapRequests;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Controller;

class SwapController extends Controller
{
    public function indexAction($url = '')
    {
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
                $request = new SwapRequests();
                $request->new_address = $post['new_address'];
                $request->immediate_address = $resultd;
                $request->create();

                echo "Your immediate address is: ".$resultd;
            }
        }
    }

    public function resultAction()
    {

    }
}
