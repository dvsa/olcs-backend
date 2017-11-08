<?php

namespace Dvsa\Olcs\Api\Domain\Command\Application\Grant;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Create tasks related to person deletion after a grant
 */
final class CreatePostDeletePeopleGrantTask extends AbstractCommand
{
    /**
     * @var int
     * @Transfer\Filter({"name":"Zend\Filter\Digits"})
     * @Transfer\Validator({"name":"Zend\Validator\Digits"})
     * @Transfer\Validator({"name":"Zend\Validator\GreaterThan", "options": {"min": 0}})
     */
    protected $applicationId;

    /**
     * @return int
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }
}
