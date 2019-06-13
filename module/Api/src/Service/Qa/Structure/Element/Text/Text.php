<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableText;

class Text implements ElementInterface
{
    /** @var TranslateableText $label */
    private $label;

    /** @var TranslateableText|null $hint */
    private $hint;

    /** @var string $value */
    private $value;

    /**
     * Create instance
     *
     * @param TranslateableText $label
     * @param TranslateableText $hint (optional)
     * @param string $value
     *
     * @return Text
     */
    public function __construct(
        TranslateableText $label,
        ?TranslateableText $hint = null,
        $value
    ) {
        $this->label = $label;
        $this->hint = $hint;
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        $representation = [
            'label' => $this->label->getRepresentation(),
            'value' => $this->value,
        ];

        if (!is_null($this->hint)) {
            $representation['hint'] = $this->hint->getRepresentation();
        }

        return $representation;
    }

    /**
     * Get the embedded hint TranslateableText instance
     *
     * @return TranslateableText|null
     */
    public function getHint()
    {
        return $this->hint;
    }
}
