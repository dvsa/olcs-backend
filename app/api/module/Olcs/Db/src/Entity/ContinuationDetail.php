<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ContinuationDetail Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="continuation_detail",
 *    indexes={
 *        @ORM\Index(name="ix_continuation_detail_continuation_id", columns={"continuation_id"}),
 *        @ORM\Index(name="ix_continuation_detail_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_continuation_detail_checklist_document_id", columns={"checklist_document_id"}),
 *        @ORM\Index(name="ix_continuation_detail_status", columns={"status"}),
 *        @ORM\Index(name="ix_continuation_detail_received", columns={"received"}),
 *        @ORM\Index(name="ix_continuation_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_continuation_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class ContinuationDetail implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOne,
        Traits\StatusManyToOneAlt1,
        Traits\CustomVersionField;

    /**
     * Checklist document
     *
     * @var \Olcs\Db\Entity\Document
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Document")
     * @ORM\JoinColumn(name="checklist_document_id", referencedColumnName="id", nullable=true)
     */
    protected $checklistDocument;

    /**
     * Continuation
     *
     * @var \Olcs\Db\Entity\Continuation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Continuation")
     * @ORM\JoinColumn(name="continuation_id", referencedColumnName="id", nullable=false)
     */
    protected $continuation;

    /**
     * Received
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="received", nullable=false, options={"default": 0})
     */
    protected $received = 0;

    /**
     * Tot auth vehicles
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_auth_vehicles", nullable=true)
     */
    protected $totAuthVehicles;

    /**
     * Tot community licences
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_community_licences", nullable=true)
     */
    protected $totCommunityLicences;

    /**
     * Tot psv discs
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_psv_discs", nullable=true)
     */
    protected $totPsvDiscs;

    /**
     * Set the checklist document
     *
     * @param \Olcs\Db\Entity\Document $checklistDocument
     * @return ContinuationDetail
     */
    public function setChecklistDocument($checklistDocument)
    {
        $this->checklistDocument = $checklistDocument;

        return $this;
    }

    /**
     * Get the checklist document
     *
     * @return \Olcs\Db\Entity\Document
     */
    public function getChecklistDocument()
    {
        return $this->checklistDocument;
    }

    /**
     * Set the continuation
     *
     * @param \Olcs\Db\Entity\Continuation $continuation
     * @return ContinuationDetail
     */
    public function setContinuation($continuation)
    {
        $this->continuation = $continuation;

        return $this;
    }

    /**
     * Get the continuation
     *
     * @return \Olcs\Db\Entity\Continuation
     */
    public function getContinuation()
    {
        return $this->continuation;
    }

    /**
     * Set the received
     *
     * @param string $received
     * @return ContinuationDetail
     */
    public function setReceived($received)
    {
        $this->received = $received;

        return $this;
    }

    /**
     * Get the received
     *
     * @return string
     */
    public function getReceived()
    {
        return $this->received;
    }

    /**
     * Set the tot auth vehicles
     *
     * @param int $totAuthVehicles
     * @return ContinuationDetail
     */
    public function setTotAuthVehicles($totAuthVehicles)
    {
        $this->totAuthVehicles = $totAuthVehicles;

        return $this;
    }

    /**
     * Get the tot auth vehicles
     *
     * @return int
     */
    public function getTotAuthVehicles()
    {
        return $this->totAuthVehicles;
    }

    /**
     * Set the tot community licences
     *
     * @param int $totCommunityLicences
     * @return ContinuationDetail
     */
    public function setTotCommunityLicences($totCommunityLicences)
    {
        $this->totCommunityLicences = $totCommunityLicences;

        return $this;
    }

    /**
     * Get the tot community licences
     *
     * @return int
     */
    public function getTotCommunityLicences()
    {
        return $this->totCommunityLicences;
    }

    /**
     * Set the tot psv discs
     *
     * @param int $totPsvDiscs
     * @return ContinuationDetail
     */
    public function setTotPsvDiscs($totPsvDiscs)
    {
        $this->totPsvDiscs = $totPsvDiscs;

        return $this;
    }

    /**
     * Get the tot psv discs
     *
     * @return int
     */
    public function getTotPsvDiscs()
    {
        return $this->totPsvDiscs;
    }
}
