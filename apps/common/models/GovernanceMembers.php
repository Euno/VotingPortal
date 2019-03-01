<?php
namespace EunoVoting\Common\Models;
use EunoVoting\Common\Libraries\jsonRPCClient;
use Phalcon\Mvc\Model\Behavior\SoftDelete;

class GovernanceMembers extends \Phalcon\Mvc\Model
{
    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", length=45, nullable=false)
     */
    public $telegram_username;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $discord;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $twitter_handle;

    /**
     *
     * @var string
     * @Column(type="string", length=45, nullable=false)
     */
    public $masternode_address;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $masternode_ipaddress_port;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $signed_msg;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    public $access;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    public $date;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    public $deleted;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    /*public function getSource()
    {
        //return 'governance_members';
    }*/

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Admins[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Admins
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function beforeCreate()
    {
        $this->date = time();
        $this->deleted = 0;
    }

    public function initialize()
    {
        $this->addBehavior(new softDelete([
            "field" => "deleted",
            "value" => 1
        ]));
    }

    /**
     * Check the signed message related to this model
     *
     * @return mixed
     */
    public function checkHash()
    {
        $config = $this->getDI()->get('config');
        $connect_string = sprintf('http://%s:%s@%s:%s/', $config->eunod->user, $config->eunod->pass, $config->eunod->host, $config->eunod->port);
        $coind = new jsonRPCClient($connect_string);

        return $coind->verifymessage($this->masternode_address, $this->signed_msg, $this->telegram_username);
    }
}