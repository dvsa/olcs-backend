<?php

namespace Dvsa\Olcs\Api\Domain\Command\Document;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Create Document
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreateDocumentSpecific extends AbstractCommand
{
    protected $filename;

    protected $identifier;

    protected $size;

    protected $application;

    protected $busReg;

    protected $case;

    protected $irfoOrganisation;

    protected $submission;

    protected $trafficArea;

    protected $transportManager;

    protected $licence;

    protected $operatingCentre;

    protected $opposition;

    protected $continuationDetail;

    protected $category;

    protected $subCategory;

    protected $description;

    protected $isExternal;

    protected $isScan = 0;

    protected $isEbsrPack = 0;

    protected $issuedDate;

    protected $metadata;

    protected $user;

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

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
    public function getBusReg()
    {
        return $this->busReg;
    }

    /**
     * @return mixed
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * @return mixed
     */
    public function getIrfoOrganisation()
    {
        return $this->irfoOrganisation;
    }

    /**
     * @return mixed
     */
    public function getSubmission()
    {
        return $this->submission;
    }

    /**
     * @return mixed
     */
    public function getTrafficArea()
    {
        return $this->trafficArea;
    }

    /**
     * @return mixed
     */
    public function getTransportManager()
    {
        return $this->transportManager;
    }

    /**
     * @return mixed
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * @return mixed
     */
    public function getOperatingCentre()
    {
        return $this->operatingCentre;
    }

    /**
     * @return mixed
     */
    public function getOpposition()
    {
        return $this->opposition;
    }

    /**
     * @return int
     */
    public function getContinuationDetail()
    {
        return $this->opposition;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getSubCategory()
    {
        return $this->subCategory;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getIsExternal()
    {
        return $this->isExternal;
    }

    /**
     * @return mixed
     */
    public function getIsScan()
    {
        return $this->isScan;
    }

    /**
     * @return bool
     */
    public function getIsEbsrPack()
    {
        return $this->isEbsrPack;
    }

    /**
     * @return mixed
     */
    public function getIssuedDate()
    {
        return $this->issuedDate;
    }

    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
}
