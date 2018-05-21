<?php

namespace JEL\Agents\Entities;

use Illuminate\Database\Eloquent\Model;
use JEL\Users\Entities\User;

/**
 * Class Group
 *
 * Allows to interact with the group table
 *
 * @package JEL\Agents\Whitelabels
 * @author  Arniel Serrano
 * @author  Eborio Linarez
 */
class Group extends Model
{
    /**
     * Table
     *
     * @var string
     */
    protected $table = 'groups';

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
    protected $fillable = ['description', 'whitelabel'];

    /**
     * Timestamps
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relationship with Agent entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function agents()
    {
        return $this->belongsToMany(Agent::class, 'agentgroup', 'group', 'agent');
    }

    /**
     * Relationship with User entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'groupuser', 'group', 'user');
    }
}
