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
     * @Transfer\Filter({"name":"Laminas\Filter\Digits"})
     * @Transfer\Validator({"name":"Laminas\Validator\Digits"})
     * @Transfer\Validator({"name":"Laminas\Validator\GreaterThan", "options": {"min": 0}})
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
