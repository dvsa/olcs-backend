<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;

class GenericAnswerFetcher
{
    /** @var NamedAnswerFetcher */
    private $namedAnswerFetcher;

    public function __construct(NamedAnswerFetcher $namedAnswerFetcher)
    {
        $this->namedAnswerFetcher = $namedAnswerFetcher;
    }

    /**
     * Retrieve an answer from the appropriate location within the post data
     *
     * @param ApplicationStepEntity $applicationStep
     * @param array $postData
     *
     * @return mixed
     */
    public function fetch(ApplicationStepEntity $applicationStep, array $postData)
    {
        return $this->namedAnswerFetcher->fetch($applicationStep, $postData, 'qaElement');
    }
}
