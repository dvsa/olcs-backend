<?php

namespace Dvsa\Olcs\Api\Domain;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;

/**
 * OpenAmUser Aware Interface
 */
interface OpenAmUserAwareInterface
{
    /**
     * @param UserInterface $service
     */
    public function setOpenAmUser(UserInterface $service);

    /**
     * @return UserInterface
     */
    public function getOpenAmUser();
}
