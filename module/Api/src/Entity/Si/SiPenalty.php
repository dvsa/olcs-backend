<?php

namespace Dvsa\Olcs\Api\Entity\Si;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as SiEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenaltyType as SiPenaltyTypeEntity;

/**
 * SiPenalty Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="si_penalty",
 *    indexes={
 *        @ORM\Index(name="ix_si_penalty_serious_infringement_id", columns={"serious_infringement_id"}),
 *        @ORM\Index(name="ix_si_penalty_si_penalty_type_id", columns={"si_penalty_type_id"}),
 *        @ORM\Index(name="ix_si_penalty_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_si_penalty_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class SiPenalty extends AbstractSiPenalty
{
    /**
     * SiPenalty constructor.
     * @param SeriousInfringement $seriousInfringement
     * @param SiPenaltyType $siPenaltyType
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string $imposed
     * @param string $reasonNotImposed
     */
    public function __construct(
        SiEntity $seriousInfringement,
        SiPenaltyTypeEntity $siPenaltyType,
        \DateTime $startDate,
        \DateTime $endDate,
        $imposed,
        $reasonNotImposed
    ) {
        $this->seriousInfringement = $seriousInfringement;
        $this->update($siPenaltyType, $startDate, $endDate, $imposed, $reasonNotImposed);
    }

    /**
     * @param SiPenaltyType $siPenaltyType
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string $imposed
     * @param string $reasonNotImposed
     */
    public function update(
        SiPenaltyTypeEntity $siPenaltyType,
        \DateTime $startDate,
        \DateTime $endDate,
        $imposed,
        $reasonNotImposed
    ) {
        $this->siPenaltyType = $siPenaltyType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->imposed = $imposed;
        $this->reasonNotImposed = $reasonNotImposed;
    }
}
