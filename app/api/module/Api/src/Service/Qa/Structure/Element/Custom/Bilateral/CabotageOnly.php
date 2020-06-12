<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

class CabotageOnly implements ElementInterface
{
    /** @var bool|null */
    private $yesNo;

    /** @var string */
    private $countryName;

    /**
     * Create instance
     *
     * @param bool|null $yesNo
     * @param string $countryName
     *
     * @return CabotageOnly
     */
    public function __construct($yesNo, $countryName)
    {
        $this->yesNo = $yesNo;
        $this->countryName = $countryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        return [
            'yesNo' => $this->yesNo,
            'countryName' => $this->countryName,
        ];
    }
}
