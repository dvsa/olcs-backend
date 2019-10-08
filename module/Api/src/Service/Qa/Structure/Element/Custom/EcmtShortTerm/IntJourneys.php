<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio\Radio;

class IntJourneys implements ElementInterface
{
    /** @var bool */
    private $showNiWarning;

    /** @var Radio */
    private $radio;

    /**
     * Create instance
     *
     * @param bool $showNiWarning
     * @param Radio $radio
     *
     * @return IntJourneys
     */
    public function __construct($showNiWarning, Radio $radio)
    {
        $this->showNiWarning = $showNiWarning;
        $this->radio = $radio;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        return [
            'showNiWarning' => $this->showNiWarning,
            'radio' => $this->radio->getRepresentation(),
        ];
    }
}
