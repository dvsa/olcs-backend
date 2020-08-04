<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\NamedAnswerFetcher;

class NoOfPermitsAnswerFetcher
{
    /** @var NamedAnswerFetcher */
    private $namedAnswerFetcher;

    /**
     * Create service instance
     *
     * @param NamedAnswerFetcher $namedAnswerFetcher
     *
     * @return NoOfPermitsAnswerFetcher
     */
    public function __construct(NamedAnswerFetcher $namedAnswerFetcher)
    {
        $this->namedAnswerFetcher = $namedAnswerFetcher;
    }

    /**
     * Derive the number of permits for a given emissions type from the post data
     *
     * @param ApplicationStepEntity $applicationStep
     * @param array $postData
     * @param string $elementName
     *
     * @return int
     */
    public function fetch(ApplicationStepEntity $applicationStepEntity, array $postData, $elementName)
    {
        try {
            $permitsRequired = $this->namedAnswerFetcher->fetch($applicationStepEntity, $postData, $elementName);
        } catch (NotFoundException $e) {
            $permitsRequired = 0;
        }

        if ($permitsRequired == '') {
            $permitsRequired = 0;
        }

        return $permitsRequired;
    }
}
