<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\ValidatorList;

class ElementGeneratorContext
{
    /** @var ValidatorList */
    private $validatorList;

    /** @var QaContext */
    private $qaContext;

    /**
     * Create instance
     *
     * @param ValidatorList $validatorList
     * @param QaContext $qaContext
     *
     * @return ElementGeneratorContext
     */
    public function __construct(ValidatorList $validatorList, QaContext $qaContext)
    {
        $this->validatorList = $validatorList;
        $this->qaContext = $qaContext;
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

    /*
     * Get the answer value associated with this context
     *
     * @return mixed
     */
    public function getAnswerValue()
    {
        return $this->qaContext->getAnswerValue();
    }
}
