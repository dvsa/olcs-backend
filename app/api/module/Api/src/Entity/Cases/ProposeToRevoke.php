<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc;
use Dvsa\Olcs\Api\Entity\User\User;

/**
 * ProposeToRevoke Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="propose_to_revoke",
 *    indexes={
 *        @ORM\Index(name="ix_propose_to_revoke_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_propose_to_revoke_presiding_tc_id", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="ix_propose_to_revoke_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_propose_to_revoke_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class ProposeToRevoke extends AbstractProposeToRevoke
{
    /**
     * ProposeToRevoke constructor.
     *
     * @param Cases       $case               case
     * @param array       $reasons            reasons
     * @param PresidingTc $presidingTc        presidingTc
     * @param \DateTime   $ptrAgreedDate      ptrAgreedDate
     * @param User|null   $assignedCaseworker assignedCaseworker
     */
    public function __construct(
        Cases $case,
        array $reasons,
        PresidingTc $presidingTc,
        \DateTime $ptrAgreedDate,
        User $assignedCaseworker = null
    ) {
        parent::__construct();
        $this->case = $case;
        $this->reasons = $reasons;
        $this->presidingTc = $presidingTc;
        $this->ptrAgreedDate = $ptrAgreedDate;
        $this->assignedCaseworker = $assignedCaseworker;
    }

    /**
     * @param array       $reasons            reasons
     * @param PresidingTc $presidingTc        presidingTc
     * @param \DateTime   $ptrAgreedDate      ptrAgreedDate
     * @param User|null   $assignedCaseworker assignedCaseworker
     *
     * @return void
     */
    public function update(
        array $reasons,
        PresidingTc $presidingTc,
        \DateTime $ptrAgreedDate,
        User $assignedCaseworker = null
    ) {
        $this->reasons = $reasons;
        $this->presidingTc = $presidingTc;
        $this->ptrAgreedDate = $ptrAgreedDate;
        $this->assignedCaseworker = $assignedCaseworker;
    }
}
