<?php
namespace EunoVoting\VotingBackend\Controllers;

use EunoVoting\Common\Models\GovernanceMembers;
use Phalcon\Mvc\View;

class GovernancemembersController extends ControllerBase
{
    public function indexAction()
    {
        $nodesFetch = file_get_contents('https://explorer.euno.co/api/getmasternodes');

        $nodes = [];
        if($nodesFetch)
        {
            $nodes = json_decode($nodesFetch, true);
        }

        $governanceMembers = GovernanceMembers::find(['deleted = 0']);

        $this->view->nodes = $nodes;
        $this->view->governanceMembers = $governanceMembers;
    }

    public function editAction($id = false)
    {
        if($id)
        {
            $governanceMember = GovernanceMembers::findFirst($id);
        }
        else
        {
            $governanceMember = new GovernanceMembers();
        }

        $this->view->governanceMember = $governanceMember;
    }

    public function saveAction($id = false)
    {
        if($this->request->isPost())
        {
            $post = $this->request->getPost();

            //print '<pre>';
            //print_r($post);
            //exit;

            if ($id)
            {
                $governanceMember = GovernanceMembers::findFirst($id);
            }
            else
            {
                $governanceMember = new GovernanceMembers();
            }

            $governanceMember->save($post);

            return $this->response->redirect('governancemembers');
        }
    }

    public function deleteAction($id = false)
    {
        GovernanceMembers::findFirst($id)->delete();

        return $this->response->redirect('governancemembers');
    }
}