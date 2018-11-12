<?php
use Phalcon\Mvc\View;

class AdminsController extends ControllerBase
{
    public function indexAction()
    {
        $admins = Users::find();

        $this->view->admins = $admins;
    }

    public function editAction($id = false)
    {
        if($id)
        {
            $admin = Users::findFirst($id);
        }
        else
        {
            $admin = new Users();
        }

        $this->view->admin = $admin;
    }

    public function saveAction($id = false)
    {
        if($this->request->isPost())
        {
            $post = $this->request->getPost();

            if ($id)
            {
                $admin = Users::findFirst($id);
            }
            else
            {
                $admin = new Users();
            }

            if( $post['password'] == "" )
            {
                unset( $post['password'] );
            }
            else
            {
                $post['password'] = $this->security->hash( $post['password'] );
            }

            $admin->save($post);

            return $this->response->redirect( 'admins');
        }
    }

    public function deleteAction($id = false)
    {
        Users::findFirst($id)->delete();

        return $this->response->redirect( 'admins');
    }
}