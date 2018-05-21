<?php

namespace JEL\Agents\Repositories;

use JEL\Core\Entities\Country;

/**
 * Class CountriesRepo
 *
 * Repository to manage the data of the Country entity
 *
 * @package JEL\Agents\Repositories
 * @author  Nelson Marcano
 */
class CountriesRepo
{
    /**
     * Get all countries
     *
     * @return mixed
     */
    public function all()
    {
        return Country::all(array('code as value', 'country as text'));
    }
}
