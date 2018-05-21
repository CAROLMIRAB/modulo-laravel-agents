<?php

namespace JEL\Agents\Repositories;

use JEL\Agents\Entities\Agent;
use JEL\Agents\Entities\Group;
use JEL\Users\Entities\User;

/**
 * Class GroupsRepo
 *
 * Repository to manage the data of the Group entity
 *
 * @package JEL\Users\Repositories

 * @author  Eborio Linarez
 */
class GroupsRepo
{
    /**
     * Get agents groups
     *
     * @param $agent Agent ID
     * @return mixed
     */
    public function agentsGroups($agent, $whitelabel)
    {
        $groups = Group::select('groups.id', 'description')
            ->join('agentgroup', 'groups.id', '=', 'agentgroup.group')
            ->where('agent', $agent)
            ->where('whitelabel', $whitelabel)
            ->get();
        return $groups;
    }

    /**
     * Get all groups
     *
     * @param $whitelabel Whitelabel ID
     * @return mixed
     */
    public function all($whitelabel)
    {
        $groups = Group::orderBy('description', 'asc')
            ->where('whitelabel', $whitelabel)
            ->get();
        return $groups;
    }

    /**
     * Create an group
     *
     * @param $data Group data
     * @return Group
     */
    public function create($agent, $data)
    {
        $group = new Group();
        $group->fill($data);
        $group->save();
        $group->agents()->attach($agent);
        return $group;
    }

    /**
     * Delete groups
     *
     * @param $group Group ID
     * @return mixed
     */
    public function delete($group)
    {
        $group = Group::find($group);
        $group->delete();
        return $group;
    }

    /**
     * Delete users
     *
     * @param $user User ID
     * @return mixed
     */
    public function deleteUsers($group, $user)
    {
        $group = Group::find($group);
        $group->users()->detach($user);
        return $group;
    }

    /**
     * Get users groups
     *
     * @param $user User ID
     * @return mixed
     */
    public function usersGroups($user)
    {
        $groups = Group::select('groups.id', 'description')
            ->join('groupuser', 'groups.id', '=', 'groupuser.group')
            ->where('user', $user)
            ->get();
        return $groups;
    }

    /**
     * Get agents users groups
     *
     * @param $agent
     * @param $user 
     * @param $whitelabel
     * @return mixed
     */
    public function agentsUsersGroups($agent, $user, $whitelabel)
    {

        $users = Agent::select('users.id')
            ->join('agentgroup', 'agentgroup.agent', '=','agents.user')
            ->join('groups', 'groups.id', '=', 'agentgroup.group')
            ->join('groupuser', 'groupuser.group', '=', 'groups.id')
            ->join('users', 'users.id', '=', 'groupuser.user')
            ->where('groups.whitelabel', $whitelabel)
            ->where('agents.user', $agent)
            ->where('users.id', $user)
            ->where('groups.whitelabel', $whitelabel)
            ->get();
        return $users;
    }
}
