<?php
namespace EunoVoting\Common\Models;

class VotingsAnswers extends \Phalcon\Mvc\Model
{
    public $id;

    public $voting_id;

    public $answer;

    public function getSource()
    {
        return 'votings_answers';
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
        $this->belongsTo('voting_id', 'EunoVoting\Common\Models\Votings', 'id', ['alias' => 'Voting']);
    }
}
