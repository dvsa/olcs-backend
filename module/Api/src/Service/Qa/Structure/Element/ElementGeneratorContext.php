<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\ElementContainer;
use Dvsa\Olcs\Api\Service\Qa\Structure\ValidatorList;

class ElementGeneratorContext
{
    /** @var ValidatorList */
    private $validatorList;

    /** @var QaContext */
    private $qaContext;

    /** @var string */
    private $elementContainer;

    /**
     * Create instance
     *
     * @param ValidatorList $validatorList
     * @param QaContext $qaContext
     * @param string $elementContainer
     *
     * @return ElementGeneratorContext
     */
    public function __construct(ValidatorList $validatorList, QaContext $qaContext, $elementContainer)
    {
        $this->validatorList = $validatorList;
        $this->qaContext = $qaContext;
        $this->elementContainer = $elementContainer;
    }

    /**
     * Get the embedded ValidatorList instance
     *
     * @return ValidatorList
     */
    public function getValidatorList()
    {
        return $this->validatorList;
    }

    /**
     * Get the embedded ApplicationStepEntity instance
     *
     * @return ApplicationStepEntity
     */
    public function getApplicationStepEntity()
    {
        return $this->qaContext->getApplicationStepEntity();
    }

    /**
     * Get the embedded QaEntityInterface instance
     *
     * @return QaEntityInterface
     */
    public function getQaEntity()
    {
        return $this->qaContext->getQaEntity();
    }

    /**
     * Get the embedded QaContext instance
     *
     * @return QaContext
     */
    public function getQaContext()
    {
        return $this->qaContext;
    }

    /**
     * Get the answer value associated with this context
     *
     * @return mixed
     */
    public function getAnswerValue()
    {
        return $this->qaContext->getAnswerValue();
    }

    /**
     * Whether the context was raised from within a selfserve page request
     *
     * @return bool
     */
    public function isSelfservePageContainer()
    {
        return $this->elementContainer == ElementContainer::SELFSERVE_PAGE;
    }
}
