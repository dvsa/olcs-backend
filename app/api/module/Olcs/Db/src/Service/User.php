<?php
namespace Olcs\Db\Service;

class User extends ServiceAbstract
{
    /**
     * Returns an indexed array of valid search terms for this service / entity.
     *
     * @return array
     */
    public function getValidSearchFields()
    {
        return array(
            'username'
        );
    }
}
