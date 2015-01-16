<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Irfo organisation many to one trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait IrfoOrganisationManyToOne
{
    /**
     * Irfo organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation")
     * @ORM\JoinColumn(name="irfo_organisation_id", referencedColumnName="id", nullable=true)
     */
    protected $irfoOrganisation;

    /**
     * Set the irfo organisation
     *
     * @param \Olcs\Db\Entity\Organisation $irfoOrganisation
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIrfoOrganisation($irfoOrganisation)
    {
        $this->irfoOrganisation = $irfoOrganisation;

        return $this;
    }

    /**
     * Get the irfo organisation
     *
     * @return \Olcs\Db\Entity\Organisation
     */
    public function getIrfoOrganisation()
    {
        return $this->irfoOrganisation;
    }
}
