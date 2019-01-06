<?php
namespace EunoVoting\VotingBackend\Plugins;

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Http\Response as Response;
/**
 * SecurityPlugin
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
class SecurityPlugin extends Plugin
{
    /**
     * Returns an existing or new access control list
     *
     * @returns AclList
     */
    public function getAcl()
    {
        if (!isset($this->persistent->acl)) {
            $acl = new AclList();
            $acl->setDefaultAction(Acl::DENY);
            // Register roles
            $roles = [
                'users'  => new Role(
                    'Users',
                    'Member privileges, granted after sign in.'
                ),
                'guests' => new Role(
                    'Guests',
                    'Anyone browsing the site who is not signed in is considered to be a "Guest".'
                )
            ];
            foreach ($roles as $role) {
                $acl->addRole($role);
            }

            //Public area resources
            $publicResources = [
                'auth'    => ['index', 'start', 'end']
            ];

            foreach ($publicResources as $resource => $actions) {
                $acl->addResource(new Resource($resource), $actions);
            }

            //Grant access to public areas to both users and guests
            foreach ($roles as $role) {
                foreach ($publicResources as $resource => $actions) {
                    foreach ($actions as $action){
                        $acl->allow($role->getName(), $resource, $action);
                    }
                }
            }

            //The acl is stored in session, APC would be useful here too
            $this->persistent->acl = $acl;
        }
        return $this->persistent->acl;
    }
    /**
     * This action is executed before execute any action in the application
     *
     * @param Event $event
     * @param Dispatcher $dispatcher
     * @return bool
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {

        $auth = $this->session->get('auth');
        if (!$auth){
            $role = 'Guests';
        } else {
            $role = 'Users';
        }

        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        $acl = $this->getAcl();

        if($role == 'Guests') {
            $allowed = $acl->isAllowed($role, $controller, $action);
            if ($allowed != Acl::ALLOW) {
                $dispatcher->forward([
                    'module' => 'votingbackend',
                    'controller' => 'auth',
                    'action' => 'index'
                ]);
                $this->session->destroy();
                return false;
            }
        }
    }
}