<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Dvsa\Olcs\Api\Entity\System\RefData;
use Doctrine\ORM\Mapping as ORM;

/**
 * ConditionUndertaking Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="condition_undertaking",
 *    indexes={
 *        @ORM\Index(name="ix_condition_undertaking_added_via", columns={"added_via"}),
 *        @ORM\Index(name="ix_condition_undertaking_attached_to", columns={"attached_to"}),
 *        @ORM\Index(name="ix_condition_undertaking_condition_type", columns={"condition_type"}),
 *        @ORM\Index(name="ix_condition_undertaking_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_condition_undertaking_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_condition_undertaking_operating_centre_id", columns={"operating_centre_id"}),
 *        @ORM\Index(name="ix_condition_undertaking_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_condition_undertaking_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_condition_undertaking_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(
 *            name="ix_condition_undertaking_lic_condition_variation_id",
 *            columns={"lic_condition_variation_id"}
 *        ),
 *        @ORM\Index(name="ix_condition_undertaking_approval_user_id", columns={"approval_user_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_condition_undertaking_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class ConditionUndertaking extends AbstractConditionUndertaking
{
    const ATTACHED_TO_LICENCE = 'cat_lic';
    const ATTACHED_TO_OPERATING_CENTRE = 'cat_oc';

    const ADDED_VIA_CASE = 'cav_case';
    const ADDED_VIA_LICENCE = 'cav_lic';
    const ADDED_VIA_APPLICATION = 'cav_app';

    const TYPE_CONDITION = 'cdt_con';
    const TYPE_UNDERTAKING = 'cdt_und';

    const SMALL_VEHICLE_UNDERTAKINGS = 'Small vehicles undertakings';

    const SMALL_VEHICLE_UNERRTAKINGS_NOTES =
        'Small vehicles undertakings
(a) The operator will advise the Traffic Commissioner of the make, model and ' .
    'registration number of vehicles used under that licence, and will advise of any changes.
(b) In respect of any vehicle with eight or less passengers seats used under the licence, ' .
    'the operator will provide an audit trail to the Traffic Commissioner or any enforcement body on ' .
    'request, that demonstrates compliance with PSV requirements. This includes paperwork as to how in ' .
    'respect of any service separate fares were paid and one of the two conditions set out in Question 1 ' .
    'were met. Note this undertaking does not apply when the vehicle is being used under the provisions of Section 79A.
(c) Each small vehicle to be used under the licence will have a V5C registration certificate, and the ' .
    'operator must possess and produce, when asked to do so, a document confirming this.
(d) Each small vehicle will receive a full safety inspection (maximum every 10 weeks) in premises suitable for ' .
    'the vehicle to ensure that its roadworthiness is maintained. Records of all inspections must be kept in ' .
    'accordance with the Guide to Maintaining Roadworthiness.
(e) At no time will the small vehicle carry more than eight passengers.
(f) The operator will at all times comply with the legislation in respect of the charging of separate fares and ' .
    'retain 12 months’ evidence of this compliance for each journey.
(g) Drivers of small vehicles will carry with them documentary evidence that separate fares have ' .
    'been charged for the current journey.
(h) The operator will not use a vehicle that does not meet the ECWVTA standards, British construction ' .
    'and use requirements or the Road Vehicles Approval Regulations 2009 (as amended).
(i) The operator or driver will not break the alcohol laws.';

    /**
     * Construct Condition Undertaking entity
     * @param RefData $conditionType
     * @param String $isDraft Y|N
     * @param String $isFulfilled Y|N
     */
    public function __construct(
        RefData $conditionType,
        $isFulfilled,
        $isDraft
    ) {
        parent::__construct();

        $this->setConditionType($conditionType);
        $this->setIsDraft($isDraft);
        $this->setIsFulfilled($isFulfilled);
    }
}
