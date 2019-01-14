<?php
namespace EunoVoting\Common\Models;

use EunoVoting\Common\Libraries\jsonRPCClient;

class Votes extends \Phalcon\Mvc\Model
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
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $voting_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $masternode_ipaddress_port;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $masternode_address;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $answer;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $signed_msg;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    public $date;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $confirmed;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'votes';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Votes[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Votes
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function initialize()
    {
        $this->belongsTo('voting_id', 'EunoVoting\Common\Models\Votings', 'id', ['alias' => 'Voting']);
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

        return $coind->verifymessage($this->masternode_address, $this->signed_msg, $this->answer);
    }
}