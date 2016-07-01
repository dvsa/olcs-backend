<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Document;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;
use Dvsa\Olcs\Transfer\Command\Document\CreateDocument as CreateDocumentDto;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadDto;

/**
 * Can Create a Document
 */
class CanCreateDocument extends AbstractHandler
{
    /**
     * @var bool
     */
    private $valid;

    /**
     * Validate DTO
     *
     * @param CreateDocumentDto|UploadDto $dto The DTO being validated
     *
     * @return bool
     */
    public function isValid($dto)
    {
        /**
         * @todo OLCS-13189
         *
         * The validator doesn't work properly for EBSR documents, this is a temporary fix.
         * A permanent fix will be done as part of OLCS-13189. A note has been added to the ticket to ensure
         * this code is removed once the permanent fix is in place
         */
        if (method_exists($dto, 'getIsEbsrPack') && $dto->getIsEbsrPack()) {
            $this->setIsValid(true);
        }

        if ($dto->getLicence()) {
            $this->setIsValid($this->canAccessLicence($dto->getLicence()));
        }

        if ($dto->getApplication()) {
            $this->setIsValid($this->canAccessApplication($dto->getApplication()));
        }

        if ($dto->getCase()) {
            $this->setIsValid($this->canAccessCase($dto->getCase()));
        }

        if ($dto->getTransportManager()) {
            $this->setIsValid($this->canAccessTransportManager($dto->getTransportManager()));
        }

        if ($dto->getOperatingCentre()) {
            $this->setIsValid($this->canAccessOperatingCentre($dto->getOperatingCentre()));
        }

        if ($dto->getBusReg()) {
            $this->setIsValid($this->canAccessBusReg($dto->getBusReg()));
        }

        if ($dto->getIrfoOrganisation()) {
            $this->setIsValid($this->canAccessOrganisation($dto->getIrfoOrganisation()));
        }

        if ($dto->getSubmission()) {
            $this->setIsValid($this->canAccessSubmission($dto->getSubmission()));
        }

        return $this->getIsValid();
    }

    /**
     * Set whether the result of the validation
     *
     * @param bool $valid The result of one of the validations
     */
    private function setIsValid($valid)
    {
        if ($this->valid === null) {
            $this->valid = $valid;
        }
        // this is ANDed, as if more than one property is validated, they must all be true
        $this->valid = $this->valid && $valid;
    }

    /**
     * Get the result of the validation
     *
     * @return bool
     */
    private function getIsValid()
    {
        return $this->valid === true;
    }
}
