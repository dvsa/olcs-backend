<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtRemoval;

use DateTime;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date\Date as DateElement;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

class PermitStartDate implements ElementInterface
{
    const DATE_MUST_BE_BEFORE_FORMAT = 'Y-m-d';

    /** @var DateTime */
    private $dateMustBeBefore;

    /** @var DateElement */
    private $date;

    /**
     * Create instance
     *
     * @param DateTime $dateMustBeBefore
     * @param DateElement $date
     *
     * @return PermitStartDate
     */
    public function __construct(DateTime $dateMustBeBefore, DateElement $date)
    {
        $this->dateMustBeBefore = $dateMustBeBefore;
        $this->date = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        return [
            'dateMustBeBefore' => $this->dateMustBeBefore->format(self::DATE_MUST_BE_BEFORE_FORMAT),
            'date' => $this->date->getRepresentation()
        ];
    }
}
