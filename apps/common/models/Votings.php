<?php
namespace EunoVoting\Common\Models;

class Votings extends \Phalcon\Mvc\Model
{
    public $id;

    public $title;

    public $description;

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
        $this->hasMany('id', 'EunoVoting\Common\Models\Votes', 'voting_id', ['alias' => 'Votes']);
        $this->hasMany('id', 'EunoVoting\Common\Models\VotingsAnswers', 'voting_id', ['alias' => 'Answers']);
        $this->hasOne('id', 'EunoVoting\Common\Models\Votings', 'parent', ['alias' => 'SecondRound']);
    }

    public function beforeCreate()
    {
        $this->add_date = time();
        $this->active = 0;
        $this->done = 0;
    }
}
