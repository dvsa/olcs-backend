<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;

class GenericAnswerFetcher
{
    const ERR_NO_ANSWER = 'No answer data found';

    /**
     * Retrieve an answer from the appropriate location within the post data
     *
     * @return mixed
     *
     * @throws NotFoundException
     */
    public function fetch(ApplicationStepEntity $applicationStep, $postData)
    {
        $fieldsetName = $applicationStep->getFieldsetName();

        if (!isset($postData[$fieldsetName]['qaElement'])) {
            throw new NotFoundException(self::ERR_NO_ANSWER);
        }

        return $postData[$fieldsetName]['qaElement'];
    }
}
