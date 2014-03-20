<?php
namespace Olcs\Db\Service;

class User extends ServiceAbstract
{
    /**
     * Should be the FQN of the class.
     *
     * @var string
     */
    protected $entityName = '\OlcsEntities\Entity\User';

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
