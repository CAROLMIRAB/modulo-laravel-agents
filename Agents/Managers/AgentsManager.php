<?php

namespace JEL\Agents\Managers;

use JEL\Validators\BaseManager;

class AgentsManager extends BaseManager
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
            'user' => 'required|unique:agents,user',
            'description' => 'required',
        ];
        return $rules;
    }

}