<?php

namespace JEL\Agents\Managers;

use JEL\Validators\BaseManager;

class GroupsManager extends BaseManager
{
    /**
     * Constructor
     *
     * @param \JEL\Validators\Entity $entity
     * @param \JEL\Validators\Data $data
     */
    function __construct($entity, $data)
    {
        parent::__construct($entity, $data);
    }

    /**
     * Get validation rules
     *
     * @return mixed
     */
    public function getRules()
    {
        $rules = [
            'description' => 'required',
            'whitelabel' => 'required'
        ];
        return $rules;
    }

}