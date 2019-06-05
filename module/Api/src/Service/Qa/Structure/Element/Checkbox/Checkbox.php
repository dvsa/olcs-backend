<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Checkbox;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableText;

class Checkbox implements ElementInterface
{
    /** @var TranslateableText $label */
    private $label;

    /** @var TranslateableText $notCheckedMessage */
    private $notCheckedMessage;

    /** @var bool $checked */
    private $checked;

    /**
     * Create instance
     *
     * @param TranslateableText $label
     * @param TranslateableText $notCheckedMessage
     * @param bool $checked
     *
     * @return Checkbox
     */
    public function __construct(TranslateableText $label, TranslateableText $notCheckedMessage, $checked)
    {
        $this->label = $label;
        $this->notCheckedMessage = $notCheckedMessage;
        $this->checked = $checked;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        return [
            'label' => $this->label->getRepresentation(),
            'notCheckedMessage' => $this->notCheckedMessage->getRepresentation(),
            'checked' => ($this->checked ? true : false)
        ];
    }
}
