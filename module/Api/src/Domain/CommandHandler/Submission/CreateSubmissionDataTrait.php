<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Submission;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Create Submission Trait
 */
trait CreateSubmissionTrait
{
    /**
     * @param $publicationConfig
     * @param SubmissionLinkEntity $publicationLink
     * @param $existingContext
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Exception
     */
    private function createSubmission(
        $publicationConfig,
        SubmissionLinkEntity $publicationLink,
        $existingContext
    ) {
        $publicationLink = $this->getSubmissionGenerator()->createSubmission(
            $publicationConfig,
            $publicationLink,
            $existingContext
        );

        $this->getRepo()->save($publicationLink);

        $result = new Result();
        $result->addId('publicationLink', $publicationLink->getId());

        return $result;
    }
}
