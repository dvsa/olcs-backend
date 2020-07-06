<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

class ThirdCountry implements ElementInterface
{
    /** @var string|null */
    private $yesNo;

    /**
     * Create instance
     *
     * @param string|null $yesNo
     *
     * @return ThirdCountry
     */
    public function __construct($yesNo)
    {
        $this->yesNo = $yesNo;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        return [
            'yesNo' => $this->yesNo,
        ];
    }
}
