<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

class StandardAndCabotage implements ElementInterface
{
    /** @var string|null */
    private $value;

    /**
     * Create instance
     *
     * @param string|null $value
     *
     * @return StandardAndCabotage
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        return [
            'value' => $this->value
        ];
    }
}
