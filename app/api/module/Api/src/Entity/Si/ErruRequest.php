<?php

namespace Dvsa\Olcs\Api\Entity\Si;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CaseEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as CountryEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;

/**
 * ErruRequest Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="erru_request",
 *    indexes={
 *        @ORM\Index(name="ix_erru_request_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_erru_request_response_user_id", columns={"response_user_id"}),
 *        @ORM\Index(name="ix_erru_request_member_state_code", columns={"member_state_code"}),
 *        @ORM\Index(name="ix_erru_request_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_erru_request_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_erru_request_msi_type", columns={"msi_type"}),
 *        @ORM\Index(name="ix_erru_request_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_erru_request_workflow_id", columns={"workflow_id"}),
 *        @ORM\UniqueConstraint(name="uk_erru_request_case_id", columns={"case_id"})
 *    }
 * )
 */
class ErruRequest extends AbstractErruRequest
{
    const DEFAULT_CASE_TYPE = 'erru_case_t_msirnys'; //MSI with no response sent

    public function __construct(
        CaseEntity $case,
        RefData $msiType,
        CountryEntity $memberStateCode,
        $originatingAuthority,
        $transportUndertakingName,
        $vrm,
        $notificationNumber,
        $workflowId
    ) {
        $this->case = $case;
        $this->msiType = $msiType;
        $this->memberStateCode = $memberStateCode;
        $this->originatingAuthority = $originatingAuthority;
        $this->transportUndertakingName = $transportUndertakingName;
        $this->vrm = $vrm;
        $this->notificationNumber = $notificationNumber;
        $this->workflowId = $workflowId;
    }

    /**
     * Updates the serious infringement with an erru response
     *
     * @param UserEntity $user
     * @param \DateTime $responseDateTime
     */
    public function updateErruResponse(UserEntity $user, \DateTime $responseDateTime)
    {
        $this->setResponseUser($user);
        $this->setResponseTime($responseDateTime);
        $this->setResponseSent('Y');
    }
}
