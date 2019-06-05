<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

class FilteredTranslateableText
{
    /** @var string */
    private $filter;

    /** @var TranslateableText */
    private $translateableText;

    /**
     * Create instance
     *
     * @param string $filter
     * @param TranslateableText $translateableText
     *
     * @return FilteredTranslateableText
     */
    public function __construct($filter, TranslateableText $translateableText)
    {
        $this->filter = $filter;
        $this->translateableText = $translateableText;
    }

    /**
     * Get the representation of this class to be returned by the API endpoint
     *
     * @return array
     */
    public function getRepresentation()
    {
        return [
            'filter' => $this->filter,
            'translateableText' => $this->translateableText->getRepresentation()
        ];
    }

    /**
     * Get the embedded TranslateableText instance
     *
     * @return TranslateableText
     */
    public function getTranslateableText()
    {
        return $this->translateableText;
    }
}
