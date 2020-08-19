<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\OptionList;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableText;

class Radio implements ElementInterface
{
    /** @var OptionList */
    private $optionList;

    /** @var TranslateableText */
    private $notSelectedMessage;

    /** @var mixed */
    private $value;

    /**
     * Create instance
     *
     * @param OptionList $optionList
     * @param TranslateableText $notSelectedMessage
     * @param mixed $value
     *
     * @return Radio
     */
    public function __construct(OptionList $optionList, TranslateableText $notSelectedMessage, $value)
    {
        $this->optionList = $optionList;
        $this->notSelectedMessage = $notSelectedMessage;
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        return [
            'options' => $this->optionList->getRepresentation(),
            'notSelectedMessage' => $this->notSelectedMessage->getRepresentation(),
            'value' => $this->value
        ];
    }
}
