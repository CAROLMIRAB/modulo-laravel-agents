<?php

namespace JEL\Agents\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Agents
 *
 * Allows to interact with the Agents table
 *
 * @package JEL\Users\Whitelabels
 * @author  Arniel Serrano
 */
class Agentv2 extends Model
{
    /**
     * Table
     *
     * @var string
     */
    protected $table = 'agentsv2';

    /**
     * Connection
     *
     * @var string
     */
    protected $connection = 'jel';

    /**
     * Primary key
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = ['id','user', 'description', 'owner','dealvalue','dealtype', 'balance', 'master'];



}
