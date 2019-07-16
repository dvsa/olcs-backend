<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableText;

class Radio implements ElementInterface
{
    /** @var array */
    private $options;

    /** @var TranslateableText $notSelectedMessage */
    private $notSelectedMessage;

    /** @var mixed $value */
    private $value;

    /**
     * Create instance
     *
     * @param array $options
     * @param TranslateableText $notSelectedMessage
     * @param mixed $value
     *
     * @return Radio
     */
    public function __construct(array $options, TranslateableText $notSelectedMessage, $value)
    {
        $this->options = $options;
        $this->notSelectedMessage = $notSelectedMessage;
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        return [
            'options' => $this->options,
            'notSelectedMessage' => $this->notSelectedMessage->getRepresentation(),
            'value' => $this->value
        ];
    }
}
