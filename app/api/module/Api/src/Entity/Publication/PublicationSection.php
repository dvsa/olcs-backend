<?php

namespace Dvsa\Olcs\Api\Entity\Publication;

use Doctrine\ORM\Mapping as ORM;

/**
 * PublicationSection Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="publication_section",
 *    indexes={
 *        @ORM\Index(name="ix_publication_section_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_publication_section_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class PublicationSection extends AbstractPublicationSection
{
    const APP_NEW_SECTION = 1;
    const APP_GRANTED_SECTION = 4;
    const APP_REFUSED_SECTION = 5;
    const APP_WITHDRAWN_SECTION = 6;
    const APP_GRANT_NOT_TAKEN_SECTION = 7;

    const LIC_SURRENDERED_SECTION = 10;
    const LIC_TERMINATED_SECTION = 11;
    const LIC_REVOKED_SECTION = 12;
    const LIC_CNS_SECTION = 20;

    const HEARING_SECTION = 13;
    const DECISION_SECTION = 14;

    const TM_HEARING_SECTION = 27;
    const TM_DECISION_SECTION = 28;

    const BUS_NEW_SECTION = 21;
    const BUS_NEW_SHORT_SECTION = 22;
    const BUS_VAR_SECTION = 23;
    const BUS_VAR_SHORT_SECTION = 24;
    const BUS_CANCEL_SECTION = 25;
    const BUS_CANCEL_SHORT_SECTION = 26;
}
