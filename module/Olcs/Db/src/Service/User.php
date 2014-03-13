<?php
namespace Olcs\Db\Service;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class User extends ServiceAbstract
{
    /**
     * Should be the FQN of the class.
     *
     * @var string
     */
    protected $entityName = '\OlcsEntities\Entity\User';

    public function getRoles($user, $includePermissions = true)
    {
        if (!($user instanceof \OlcsEntities\Entity\User)) {
            $user = $this->getEntityManager()->find($this->getEntityName(), (int)$user);
        }

        $roles = $user->getRoles();

        $output = [];

        $hydrator = new DoctrineHydrator($this->getEntityManager());
        foreach ($roles as $role) {

            $extracted = $hydrator->extract($role);

            if (true === $includePermissions) {
                $role->getPermissions();
            }

            $output[] = $extracted;
        }

        return $output;
    }

    /**
     * Returns an indexed array of valid search terms for this service / entity.
     *
     * @return array
     */
    public function getValidSearchFields()
    {
        return array(
            'username',
        );
    }

}
