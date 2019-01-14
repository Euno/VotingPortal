<?php
namespace EunoVoting\Common\Models;

class Votings extends \Phalcon\Mvc\Model
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
     * @Column(type="string", length=60, nullable=false)
     */
    public $title;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $description;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $url;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $parent;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $round;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    public $add_date;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    public $start_date;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    public $end_date;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $active;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $done;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'votings';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Votings[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Votings
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
        $this->hasMany('id', 'EunoVoting\Common\Models\Votes', 'voting_id', ['alias' => 'Votes']);
        $this->hasMany('id', 'EunoVoting\Common\Models\VotingsAnswers', 'voting_id', ['alias' => 'Answers']);
        $this->hasOne('id', 'EunoVoting\Common\Models\Votings', 'parent', ['alias' => 'SecondRound']);
    }

    /**
     * Set some properties before the INSERT action
     */
    public function beforeCreate()
    {
        $this->add_date = time();
        $this->active = 0;
        $this->done = 0;
    }
}