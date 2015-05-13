<?php

/**
 * Reset Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Application;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Reset Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class ResetApplication extends AbstractCommand
{
    private $id;

    private $operatorType;

    private $licenceType;

    private $niFlag;

    private $confirm = false;

    public function getId()
    {
        return $this->id;
    }

    public function getOperatorType()
    {
        return $this->operatorType;
    }

    public function getLicenceType()
    {
        return $this->licenceType;
    }

    public function getNiFlag()
    {
        return $this->niFlag;
    }

    public function getConfirm()
    {
        return $this->confirm;
    }

    /**
     * Exchange internal values from provided array
     *
     * @param array $array
     * @return void
     */
    public function exchangeArray(array $array)
    {
        if (isset($array['id'])) {
            $this->id = $array['id'];
        }

        if (isset($array['operatorType'])) {
            $this->operatorType = $array['operatorType'];
        }

        if (isset($array['licenceType'])) {
            $this->licenceType = $array['licenceType'];
        }

        if (isset($array['niFlag'])) {
            $this->niFlag = $array['niFlag'];
        }

        if (isset($array['confirm'])) {
            $this->confirm = $array['confirm'];
        }
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'id' => $this->id,
            'operatorType' => $this->operatorType,
            'licenceType' => $this->licenceType,
            'niFlag' => $this->niFlag,
            'confirm' => $this->confirm
        ];
    }
}
