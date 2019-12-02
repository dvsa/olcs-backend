<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Common;

use DateTime;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date\Date as DateElement;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

class DateWithThreshold implements ElementInterface
{
    const DATE_THRESHOLD_FORMAT = 'Y-m-d';

    /** @var DateTime */
    private $dateThreshold;

    /** @var DateElement */
    private $date;

    /**
     * Create instance
     *
     * @param DateTime $dateThreshold
     * @param DateElement $date
     *
     * @return DateWithThreshold
     */
    public function __construct(DateTime $dateThreshold, DateElement $date)
    {
        $this->dateThreshold = $dateThreshold;
        $this->date = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        return [
            'dateThreshold' => $this->dateThreshold->format(self::DATE_THRESHOLD_FORMAT),
            'date' => $this->date->getRepresentation()
        ];
    }
}
