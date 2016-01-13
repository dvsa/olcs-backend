<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc;

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
    public function __construct(Cases $case, array $reasons, PresidingTc $presidingTc, \DateTime $ptrAgreedDate)
    {
        $this->case = $case;
        $this->reasons = $reasons;
        $this->presidingTc = $presidingTc;
        $this->ptrAgreedDate = $ptrAgreedDate;
    }

    public function update(array $reasons, PresidingTc $presidingTc, \DateTime $ptrAgreedDate)
    {
        $this->reasons = $reasons;
        $this->presidingTc = $presidingTc;
        $this->ptrAgreedDate = $ptrAgreedDate;
    }
}
