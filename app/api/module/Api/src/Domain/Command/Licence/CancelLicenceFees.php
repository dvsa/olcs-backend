<?php

/**
 * Cancel Licence Fees
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Licence;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Cancel Licence Fees
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CancelLicenceFees extends AbstractCommand
{
    private $id;

    public function getId()
    {
        return $this->id;
    }

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array)
    {
        if (isset($array['id'])) {
            $this->id = $array['id'];
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
            'id' => $this->id
        ];
    }
}
