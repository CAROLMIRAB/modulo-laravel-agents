<?php

namespace JEL\Agents\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AgentAudit
 *
 * @package JEL\Agents\Entities
 * @author David Rivero <david.dotworkers@gmail.com>
 */
class AgentAudit extends Model
{

    /**
     * Table
     *
     * @var string
     */
    protected $table = 'agentaudittransaction';

    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = ['ownerid', 'ownerbalance', 'targetuser','amount','typetransaction','description'];

    /**
     * Connection
     *
     * @var string
     */
    protected $connection = 'jel';
}