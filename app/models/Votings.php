<?php

class Votings extends \Phalcon\Mvc\Model
{
    public $id;

    public $title;

    public $url;

    public $add_date;

    public $end_date;

    public $active;

    public function getSource()
    {
        return 'votings';
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
        $this->hasMany('id', 'Votes', 'voting_id');
        $this->hasMany('id', 'VotingsAnswers', 'voting_id', ['alias' => 'Answers']);
    }

    public function beforeCreate()
    {
        $this->add_date = time();
    }
}
