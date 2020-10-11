<?php
namespace EunoVoting\VotingBackend\Controllers;

use EunoVoting\Common\Models\SwapRequests;
use EunoVoting\Common\Models\Users;
use Phalcon\Mvc\View;

class SwapsController extends ControllerBase
{
    public function indexAction($pending_only = false)
    {
        if($pending_only !== false)
        {
            $swaps = SwapRequests::find([
                'swapped = 0',
                'order' => 'date ASC'
            ]);
        }
        else
        {
            $swaps = SwapRequests::find([
                'order' => 'date ASC'
            ]);
        }

        $this->view->swaps = $swaps;
        $this->view->pending_only = $pending_only;
    }

    public function viewAction($id = false)
    {
        $swap = SwapRequests::findFirst($id);

        if(!$swap)
            $this->response->redirect('swaps');

        $call = file_get_contents("https://explorerold.euno.co/ext/getbalance/".$swap->immediate_address);

        $this->view->swap = $swap;
        $this->view->balance = ctype_digit($call) ? $call : json_decode($call);
    }

    public function saveAction($id = false)
    {
        if($this->request->isPost())
        {
            $post = $this->request->getPost();

            $swap = SwapRequests::findFirst($id);

            $swap->swapped_by = $this->session->get('auth')['id'];
            $swap->swapped_date = time();
            $swap->save($post);

            return $this->response->redirect( 'swaps');
        }
    }
}
