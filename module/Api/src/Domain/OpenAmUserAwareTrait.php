<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;

/**
 * OpenAmUser Aware
 */
trait OpenAmUserAwareTrait
{
    /**
     * @var UserInterface
     */
    protected $openAmUser;

    /**
     * @return \Dvsa\Olcs\Api\Service\OpenAm\UserInterface
     */
    public function getOpenAmUser()
    {
        return $this->openAmUser;
    }

    /**
     * @param \Dvsa\Olcs\Api\Service\OpenAm\UserInterface $openAmUser
     */
    public function setOpenAmUser(UserInterface $openAmUser)
    {
        $this->openAmUser = $openAmUser;
    }
}
