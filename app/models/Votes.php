<?php

class Votes extends \Phalcon\Mvc\Model
{
    public $id;

    public $voting_id;

    public $masternode_ipaddress_port;

    public $masternode_address;

    public $answer;

    public $signed_msg;

    public $date;

    public $confirmed;

    public function getSource()
    {
        return 'votes';
    }

    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function initialize()
    {
        $this->belongsTo('voting_id', 'Votings', 'id', ['alias' => 'Voting']);
    }

    public function checkHash()
    {
        $config = $this->getDI()->get('config');
        $connect_string = sprintf('http://%s:%s@%s:%s/', $config->eunod->user, $config->eunod->pass, $config->eunod->host, $config->eunod->port);
        $coind = new jsonRPCClient($connect_string);

        return $coind->verifymessage($this->masternode_address, $this->signed_msg, $this->answer);
    }
}
