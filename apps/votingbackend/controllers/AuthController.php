<?php
namespace EunoVoting\VotingBackend\Controllers;

use EunoVoting\Common\Models\Users;
use Phalcon\Mvc\View;

class AuthController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    private function _registerSession($user)
    {
        $this->session->set(
            'auth',
            [
                'id'   => $user->id,
                'name' => $user->name
            ]
        );
    }

    public function startAction()
    {
        if ($this->request->isPost()) {

            // Get the data from the user
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            // Find the user in the database
            $user = Users::findFirstByUsername($email);

            if ($user != false) {

                if ($this->security->checkHash($password, $user->password))
                {
                    $this->_registerSession($user);

                    $this->flash->success('Welcome ' . $user->name);

                    // Forward to the 'invoices' controller if the user is valid
                    return $this->response->redirect( 'votings');
                    
                }
                else
                {
                    $this->flash->error('Wrong email/password');
                }

            }
            else
            {

                $this->flash->error('Wrong email/password');
            }
        }

        return $this->dispatcher->forward(
            [
                'controller' => 'auth',
                'action'     => 'index'
            ]
        );
    }

    /**
     * Finishes the active session redirecting to the index
     *
     * @return unknown
     */
    public function endAction()
    {
        $this->view->disable();
        $this->session->remove('auth');
        $this->flash->success('Goodbye!');
        return $this->response->redirect('auth/index');
    }
}