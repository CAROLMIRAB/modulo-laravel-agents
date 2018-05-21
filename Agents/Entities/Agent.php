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
class Agent extends Model
{
    /**
     * Table
     *
     * @var string
     */
    protected $table = 'agents';

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
    protected $primaryKey = 'user';

    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = ['user', 'description', 'owner', 'amount', 'type', 'agenttype'];

    /**
     * Relationship with Group entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'agentgroup', 'agent', 'group');
    }
}
