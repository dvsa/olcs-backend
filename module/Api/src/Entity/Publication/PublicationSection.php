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

    const VAR_NEW_SECTION = 3;
    const VAR_GRANTED_SECTION = 8;
    const VAR_REFUSED_SECTION = 9;

    const SCHEDULE_1_NI_NEW = 29;
    const SCHEDULE_4_NEW = 16;
    const SCHEDULE_1_NI_UNTRUE = 30;
    const SCHEDULE_4_UNTRUE = 17;
    const SCHEDULE_1_NI_TRUE = 31;
    const SCHEDULE_4_TRUE = 18;

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

    /**
     * Is this publication section part of section 3 (section 3 is Schedule 41)
     *
     * @return bool
     */
    public function isSection3()
    {
        $section3Sections = [
            self::SCHEDULE_1_NI_NEW,
            self::SCHEDULE_1_NI_TRUE,
            self::SCHEDULE_1_NI_UNTRUE,
            self::SCHEDULE_4_NEW,
            self::SCHEDULE_4_TRUE,
            self::SCHEDULE_4_UNTRUE,
        ];

        return in_array($this->getId(), $section3Sections);
    }

    /**
     * Is this section a Decision section
     *
     * @return bool
     */
    public function isDecisionSection()
    {
        $sections = [
            self::APP_GRANTED_SECTION,
            self::APP_REFUSED_SECTION,
            self::APP_WITHDRAWN_SECTION,
            self::APP_GRANT_NOT_TAKEN_SECTION,
            self::VAR_GRANTED_SECTION,
            self::VAR_REFUSED_SECTION,
        ];

        return in_array($this->getId(), $sections);
    }
}
