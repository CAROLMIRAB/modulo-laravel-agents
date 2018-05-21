<?php

namespace JEL\Agents\Repositories;

use JEL\Agents\Entities\Agentv2;
use JEL\Agents\Entities\Agentv2Transaction;
use JEL\Core\Enums\TransactionType;
use JEL\Users\Entities\User;

/**
 * Class AgentsRepo
 *
 * Repository to manage the data of the Agent entity
 *
 * @package JEL\Users\Repositories
 * @author  Arniel Serrano
 * @author  Eborio Linarez
 */
class Agentsv2Repo
{

    /**
     * Create Agent
     *
     * @param $data
     * @return Agentv2
     */
    public function createAgent($data)
    {
        $ag = new Agentv2();
        $ag->fill($data);
        $ag->save();

        return $ag;
    }

    /**
     *
     * @param $data
     * @return Agentv2
     */
    public function createPlayer($agent, $user)
    {

        $ag = \DB::table('agentsv2users')->insert(['agent' => $agent, 'user' => $user]);
        return $ag;
    }

    /**
     * Find Balance owner
     *
     * @param $owner
     */
    public function findBalanceAgent($owner)
    {
        $owner = Agentv2::select('balance')->where('user', $owner)->first();
        return $owner;
    }

    /**
     * Search owner
     *
     * @param $owner
     * @param $user
     * @return mixed
     */
    public function searchOwner($owner, $user)
    {
        $owner = \DB::select("select agentsv2.user as id, description from agentsv2 where LOWER(description) like '" . strtolower($user) . "%' and owner = '$owner'");

        return $owner;
    }

    /**
     * Update balance
     *
     * @param $owner
     * @param $balance
     * @return mixed
     */
    public function updatedBalanceOwner($owner, $balance)
    {
        $owner = \DB::table('agentsv2')->where('user', '=', $owner)->update(['balance' => $balance]);
        return $owner;
    }


    /**
     * Find Dependency owner
     *
     * @param $user
     * @return mixed
     */
    public function findDependency($user)
    {
        $query = Agentv2::select('agentsv2.owner', 'users.username', 'agentsv2.description')
            ->leftJoin('users', 'users.id', '=', 'agentsv2.owner')
            ->where('agentsv2.user', $user)
            ->first();

        return $query;
    }

    /**
     * Find Dependency owner
     *
     * @param $user
     * @return mixed
     */
    public function findAgentsByOwner($user)
    {
        $query = Agentv2::select('agentsv2.user', 'users.username', 'agentsv2.description', 'agentsv2.master', 'agentsv2.balance')
            ->leftJoin('users', 'users.id', '=', 'agentsv2.user')
            ->where('agentsv2.owner', $user)
            ->get();

        return $query;
    }

    /**
     * Find Player by agent
     *
     * @param $user
     * @return mixed
     */
    public function findPlayersByAgent($user)
    {
        $query = \DB::table('agentsv2users')->select('profiles.name', 'profiles.lastname', 'users.username', 'agentsv2users.agent', 'agentsv2users.user','users.email')
            ->leftJoin('users', 'users.id', '=', 'agentsv2users.user')
            ->leftJoin('profiles', 'profiles.user', '=', 'users.id')
            ->where('agentsv2users.agent', $user)
            ->get();

        return $query;
    }

    /**
     * Find player by id
     *
     * @param $user
     * @return mixed
     */
    public function findPlayersById($user)
    {
        $query = \DB::table('agentsv2users')->select('profiles.name', 'profiles.lastname', 'users.username', \DB::raw('owners.username as agent'), \DB::raw('owners.id as agentid'), 'agentsv2users.user', 'users.locked')
            ->leftJoin('users as owners', \DB::raw('owners.id'), '=', \DB::raw('agentsv2users.agent'))
            ->leftJoin('users', 'users.id', '=', 'agentsv2users.user')
            ->leftJoin('profiles', 'profiles.user', '=', 'users.id')
            ->where('agentsv2users.user', $user)
            ->first();

        return $query;
    }

    /**
     * Find agent by id
     *
     * @param $user
     * @return mixed
     */
    public function findAgentById($user)
    {
        $query = Agentv2::select('agentsv2.user', 'users.username', 'agentsv2.description', 'agentsv2.master', \DB::raw('owners.username as owner'), \DB::raw('owners.id as ownerid'), 'users.currency', 'agentsv2.balance', 'users.locked')
            ->leftJoin('users as owners', \DB::raw('owners.id'), '=', \DB::raw('agentsv2.owner'))
            ->leftJoin('users', 'users.id', '=', 'agentsv2.user')
            ->where('agentsv2.user', $user)
            ->first();

        return $query;
    }


    public function registerTransaction($data)
    {
        $ag = new Agentv2Transaction();
        $ag->fill($data);
        $ag->save();
    }

    public function lastTransaction($user, $type, $timezone, $startDate, $endDate)
    {
        $query = \DB::select("select CASE WHEN sum(amount) IS NULL THEN 0 ELSE sum(amount) END AS amount
        from agentsv2transactions
        where agent = '$user'
        and transactiontype = '$type'
        and (created_at::TIMESTAMP WITH TIME ZONE AT TIME ZONE '$timezone')::DATE BETWEEN '$startDate' and '$endDate'");
        return $query;
    }

    /**
     * Find agent by id
     *
     * @param $user
     * @return mixed
     */
    public function agentById($user)
    {
        $query = Agentv2::select('agentsv2.user')
            ->where('agentsv2.id', $user)
            ->first();

        return $query;
    }


}