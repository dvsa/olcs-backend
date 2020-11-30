<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\CertRoadworthiness;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Common\DateWithThreshold;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

class MotExpiryDate implements ElementInterface
{
    /** @var bool */
    private $enableFileUploads;

    /** @var DateWithThreshold */
    private $dateWithThreshold;

    /**
     * Create instance
     *
     * @param bool $enableFileUploads
     * @param DateWithThreshold $dateWithThreshold
     *
     * @return MotExpiryDate
     */
    public function __construct($enableFileUploads, DateWithThreshold $dateWithThreshold)
    {
        $this->enableFileUploads = $enableFileUploads;
        $this->dateWithThreshold = $dateWithThreshold;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        return [
            'enableFileUploads' => $this->enableFileUploads,
            'dateWithThreshold' => $this->dateWithThreshold->getRepresentation(),
        ];
    }
}
