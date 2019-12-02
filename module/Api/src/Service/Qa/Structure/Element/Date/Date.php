<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

class Date implements ElementInterface
{
    /** @var string|null $value */
    private $value;

    /**
     * Create instance
     *
     * @param string $value (optional)
     *
     * @return Date
     */
    public function __construct($value = null)
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
