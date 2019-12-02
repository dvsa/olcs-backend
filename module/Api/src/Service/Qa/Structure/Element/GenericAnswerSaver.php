<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

class GenericAnswerSaver implements AnswerSaverInterface
{
    /** @var BaseAnswerSaver */
    private $baseAnswerSaver;

    /**
     * Create service instance
     *
     * @param BaseAnswerSaver $baseAnswerSaver
     *
     * @return GenericAnswerSaver
     */
    public function __construct(BaseAnswerSaver $baseAnswerSaver)
    {
        $this->baseAnswerSaver = $baseAnswerSaver;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ApplicationStep $applicationStep, IrhpApplication $irhpApplication, array $postData)
    {
        $this->baseAnswerSaver->save($applicationStep, $irhpApplication, $postData);
    }
}
