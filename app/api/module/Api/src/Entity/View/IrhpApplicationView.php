<?php

namespace Dvsa\Olcs\Api\Entity\View;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use JsonSerializable;

/**
 * IRHP Application view
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="irhp_application_view")
 */
class IrhpApplicationView implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;

    /**
     * Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * Licence id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="licence_id")
     */
    protected $licenceId;

    /**
     * Organisation id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="organisation_id")
     */
    protected $organisationId;

    /**
     * Lic no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="lic_no")
     */
    protected $licNo;

    /**
     * Application ref
     *
     * @var string
     *
     * @ORM\Column(type="string", name="application_ref")
     */
    protected $applicationRef;

    /**
     * Permits required
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="permits_required")
     */
    protected $permitsRequired;

    /**
     * Valid Permit count
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="valid_permit_count")
     */
    protected $validPermitCount;

    /**
     * Type id
     *
     * @var int
     *
     * @ORM\Column(type="string", name="type_id")
     */
    protected $typeId;

    /**
     * Type description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="type_description")
     */
    protected $typeDescription;

    /**
     * Status id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="status_id")
     */
    protected $statusId;

    /**
     * Status description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="status_description")
     */
    protected $statusDescription;

    /**
     * Date received
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="date_received")
     */
    protected $dateReceived;

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the licence id
     *
     * @return int
     */
    public function getLicenceId()
    {
        return $this->licenceId;
    }

    /**
     * Get the organisation id
     *
     * @return int
     */
    public function getOrganisationId()
    {
        return $this->organisationId;
    }

    /**
     * Get the lic no
     *
     * @return string
     */
    public function getLicNo()
    {
        return $this->licNo;
    }

    /**
     * Get the application ref
     *
     * @return string
     */
    public function getApplicationRef()
    {
        return $this->applicationRef;
    }

    /**
     * Get the permits required
     *
     * @return int
     */
    public function getPermitsRequired()
    {
        return $this->permitsRequired;
    }

    /**
     * Get the type id
     *
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Get the type description
     *
     * @return string
     */
    public function getTypeDescription()
    {
        return $this->typeDescription;
    }

    /**
     * Get the status id
     *
     * @return string
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * Get the status description
     *
     * @return string
     */
    public function getStatusDescription()
    {
        return $this->statusDescription;
    }

    /**
     * Get the date received
     *
     * @return \DateTime
     */
    public function getDateReceived()
    {
        return $this->dateReceived;
    }

    /**
     * Get valid permit count
     *
     * @return int
     */
    public function getValidPermitCount()
    {
        return $this->validPermitCount;
    }
}
