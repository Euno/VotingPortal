<?php

class Votings extends \Phalcon\Mvc\Model
{
    public $id;

    public $title;

    public $url;

    public $parent;

    public $round;

    public $add_date;

    public $start_date;

    public $end_date;

    public $active;

    public $done;

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
        $this->hasOne('id', 'Votings', 'parent', ['alias' => 'SecondRound']);
    }

    public function beforeCreate()
    {
        $this->add_date = time();
        $this->active = 0;
        $this->done = 0;
    }
}
