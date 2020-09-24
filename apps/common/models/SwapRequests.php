<?php
namespace EunoVoting\Common\Models;

class SwapRequests extends \Phalcon\Mvc\Model
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
     * @Column(type="string", length=255, nullable=false)
     */
    public $uuid;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $new_address;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $immediate_address;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $notify_address;

    /**
     *
     * @var string
     * @Column(type="integer", length=10, nullable=false)
     */
    public $date;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $swapped;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $swapped_by;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $swapped_date;


    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $deleted;


    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'swap_requests';
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
        $this->hasOne('swapped_by', 'EunoVoting\Common\Models\Users', 'id', ['alias' => 'User']);
    }

    /**
     * Set some properties before the INSERT action
     */
    public function beforeCreate()
    {
        $this->date = time();
        $this->swapped = 0;
        $this->swapped_by = 0;
        $this->deleted = 0;
    }
}
