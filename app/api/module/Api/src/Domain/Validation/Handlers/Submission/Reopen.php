<?php

/**
 * Reopen Submission
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Submission;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;

/**
 * Reopen Submission
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class Reopen extends AbstractHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        if ($this->isInternalUser() && $this->canReopenSubmission($dto->getId())) {
            return true;
        }

        return false;
    }
}
