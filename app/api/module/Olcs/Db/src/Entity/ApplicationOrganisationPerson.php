<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ApplicationOrganisationPerson Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="application_organisation_person",
 *    indexes={
 *        @ORM\Index(name="ix_application_organisation_person_person_id", columns={"person_id"}),
 *        @ORM\Index(name="ix_application_organisation_person_organisation_id", columns={"organisation_id"}),
 *        @ORM\Index(name="ix_application_organisation_person_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_application_organisation_person_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_application_organisation_person_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_application_organisation_person_person1_idx", columns={"original_person_id"})
 *    }
 * )
 */
class ApplicationOrganisationPerson implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OrganisationManyToOne,
        Traits\PersonManyToOne,
        Traits\Position45Field,
        Traits\CustomVersionField;

    /**
     * Action
     *
     * @var string
     *
     * @ORM\Column(type="string", name="action", length=1, nullable=false)
     */
    protected $action;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", inversedBy="applicationOrganisationPersons")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=false)
     */
    protected $application;

    /**
     * Original person
     *
     * @var \Olcs\Db\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Person")
     * @ORM\JoinColumn(name="original_person_id", referencedColumnName="id", nullable=true)
     */
    protected $originalPerson;

    /**
     * Set the action
     *
     * @param string $action
     * @return ApplicationOrganisationPerson
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get the action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
     * @return ApplicationOrganisationPerson
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get the application
     *
     * @return \Olcs\Db\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set the original person
     *
     * @param \Olcs\Db\Entity\Person $originalPerson
     * @return ApplicationOrganisationPerson
     */
    public function setOriginalPerson($originalPerson)
    {
        $this->originalPerson = $originalPerson;

        return $this;
    }

    /**
     * Get the original person
     *
     * @return \Olcs\Db\Entity\Person
     */
    public function getOriginalPerson()
    {
        return $this->originalPerson;
    }
}
