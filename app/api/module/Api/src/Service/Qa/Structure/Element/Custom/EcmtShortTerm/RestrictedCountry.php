<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

class RestrictedCountry
{
    /** @var string */
    private $code;

    /** @var string */
    private $labelTranslationKey;

    /** @var bool */
    private $checked;

    /**
     * Create instance
     *
     * @param string $code
     * @param string $labelTranslationKey
     * @param bool checked
     *
     * @return RestrictedCountry
     */
    public function __construct($code, $labelTranslationKey, $checked)
    {
        $this->code = $code;
        $this->labelTranslationKey = $labelTranslationKey;
        $this->checked = $checked;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        return [
            'code' => $this->code,
            'labelTranslationKey' => $this->labelTranslationKey,
            'checked' => $this->checked,
        ];
    }
}
