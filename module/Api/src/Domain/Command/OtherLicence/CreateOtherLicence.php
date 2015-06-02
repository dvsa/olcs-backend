<?php

/**
 * Create Other Licence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\OtherLicence;

use Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence;
use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Create Other Licence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class CreateOtherLicence extends AbstractCommand
{
    protected $application;

    protected $licNo;

    protected $holderName;

    protected $willSurrender;

    protected $previousLicenceType;

    protected $disqualificationDate;

    protected $disqualificationLength;

    protected $purchaseDate;

    /**
     * @return mixed
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return mixed
     */
    public function getLicNo()
    {
        return $this->licNo;
    }

    /**
     * @return mixed
     */
    public function getHolderName()
    {
        return $this->holderName;
    }

    /**
     * @return mixed
     */
    public function getWillSurrender()
    {
        return $this->willSurrender;
    }

    /**
     * @return mixed
     */
    public function getPreviousLicenceType()
    {
        return $this->previousLicenceType;
    }

    /**
     * @return mixed
     */
    public function getDisqualificationDate()
    {
        return $this->disqualificationDate;
    }

    /**
     * @return mixed
     */
    public function getDisqualificationLength()
    {
        return $this->disqualificationLength;
    }

    /**
     * @return mixed
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }
}
