<?php
namespace EunoVoting\Api\Controllers;

use EunoVoting\Common\Libraries\jsonRPCClient;
use EunoVoting\Common\Models\GovernanceMembers;
use Phalcon\Mvc\Controller;

class GovernanceController extends Controller
{
    public function initialize()
    {
        $this->view->disable();
    }

    public function indexAction($url = '')
    {
        echo json_encode([
            'test' => 123
        ]);
    }
}