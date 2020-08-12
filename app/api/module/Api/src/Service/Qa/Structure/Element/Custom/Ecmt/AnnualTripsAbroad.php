<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Text;

class AnnualTripsAbroad implements ElementInterface
{
    /** @var int */
    private $intensityWarningThreshold;

    /** @var bool */
    private $showNiWarning;

    /** @var Text */
    private $text;

    /**
     * Create instance
     *
     * @param int $intensityWarningThreshold
     * @param bool $showNiWarning
     * @param Text $text
     *
     * @return AnnualTripsAbroad
     */
    public function __construct($intensityWarningThreshold, $showNiWarning, Text $text)
    {
        $this->intensityWarningThreshold = $intensityWarningThreshold;
        $this->showNiWarning = $showNiWarning;
        $this->text = $text;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        return [
            'intensityWarningThreshold' => $this->intensityWarningThreshold,
            'showNiWarning' => $this->showNiWarning,
            'text' => $this->text->getRepresentation(),
        ];
    }
}
