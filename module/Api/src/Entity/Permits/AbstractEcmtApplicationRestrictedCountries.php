<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtApplicationRestrictedCountries Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="ecmt_application_restricted_countries",
 *    indexes={
 *        @ORM\Index(name="ix_ecmt_application_country_link_ecmt_application_id",
     *     columns={"ecmt_application_id"}),
 *        @ORM\Index(name="ix_ecmt_application_country_link_country_id", columns={"country_id"})
 *    }
 * )
 */
abstract class AbstractEcmtApplicationRestrictedCountries implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Country
     *
     * @var \Dvsa\Olcs\Api\Entity\ContactDetails\Country
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\ContactDetails\Country", fetch="LAZY")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=false)
     */
    protected $country;

    /**
     * Ecmt application
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication", fetch="LAZY")
     * @ORM\JoinColumn(name="ecmt_application_id", referencedColumnName="id", nullable=false)
     */
    protected $ecmtApplication;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Last modified
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified", nullable=true)
     */
    protected $lastModified;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Set the country
     *
     * @param \Dvsa\Olcs\Api\Entity\ContactDetails\Country $country entity being set as the value
     *
     * @return EcmtApplicationRestrictedCountries
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the country
     *
     * @return \Dvsa\Olcs\Api\Entity\ContactDetails\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the ecmt application
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication $ecmtApplication entity being set as the value
     *
     * @return EcmtApplicationRestrictedCountries
     */
    public function setEcmtApplication($ecmtApplication)
    {
        $this->ecmtApplication = $ecmtApplication;

        return $this;
    }

    /**
     * Get the ecmt application
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication
     */
    public function getEcmtApplication()
    {
        return $this->ecmtApplication;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return EcmtApplicationRestrictedCountries
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * Set the last modified
     *
     * @param \DateTime $lastModified new value being set
     *
     * @return EcmtApplicationRestrictedCountries
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * Get the last modified
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getLastModified($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->lastModified);
        }

        return $this->lastModified;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return EcmtApplicationRestrictedCountries
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }



    /**
     * Clear properties
     *
     * @param array $properties array of properties
     *
     * @return void
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {
            if (property_exists($this, $property)) {
                $this->$property = null;
            }
        }
    }
}
