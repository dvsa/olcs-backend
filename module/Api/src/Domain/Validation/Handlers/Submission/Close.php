<?php

/**
 * Close Submission
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Submission;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;

/**
 * Close Submission
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class Close extends AbstractHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        if ($this->isInternalUser() && $this->canCloseSubmission($dto->getId())) {
            return true;
        }

        return false;
    }
}
