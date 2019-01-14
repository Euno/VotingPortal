<?php
namespace EunoVoting\Common\Models;

class VotingsAnswers extends \Phalcon\Mvc\Model
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
    public $answer;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'votings_answers';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return VotingsAnswers[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return VotingsAnswers
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Initialize model relations
     */
    public function initialize()
    {
        $this->belongsTo('voting_id', 'EunoVoting\Common\Models\Votings', 'id', ['alias' => 'Voting']);
    }
}