<?php

namespace Dvsa\Olcs\Api\Service\Qa\Cqrs;

class CommandCreator
{
    /**
     * Create a new cqrs command object based upon the specified fully qualified class name and parameters
     *
     * @param string $className
     * @param array $parameters
     *
     * @return mixed
     */
    public function create($className, array $parameters)
    {
        return call_user_func([$className, 'create'], $parameters);
    }
}
