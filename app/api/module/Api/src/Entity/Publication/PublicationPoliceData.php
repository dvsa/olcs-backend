<?php

namespace Dvsa\Olcs\Api\Entity\Publication;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;

/**
 * PublicationPoliceData Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="publication_police_data",
 *    indexes={
 *        @ORM\Index(name="ix_publication_police_data_publication_link_id", columns={"publication_link_id"}),
 *        @ORM\Index(name="ix_publication_police_data_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_publication_police_data_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_publication_police_data_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class PublicationPoliceData extends AbstractPublicationPoliceData
{
    /**
     * Creates a new entity
     *
     * @param PublicationLinkEntity $publicationLink
     * @param \DateTime|null $birthDate
     * @param string $forename
     * @param string $familyName
     */
    public function __construct(PublicationLinkEntity $publicationLink, $birthDate, $forename, $familyName)
    {
        $this->create($publicationLink, $birthDate, $forename, $familyName);
    }

    /**
     * Creates a new entity
     *
     * @param PublicationLinkEntity $publicationLink
     * @param \DateTime|null $birthDate
     * @param string $forename
     * @param string $familyName
     */
    private function create(PublicationLinkEntity $publicationLink, $birthDate, $forename, $familyName)
    {
        $this->publicationLink = $publicationLink;
        $this->birthDate = $birthDate;
        $this->forename = $forename;
        $this->familyName = $familyName;
    }
}
