<?php

namespace Dvsa\Olcs\Api\Entity\Si;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CaseEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as CountryEntity;
use Dvsa\Olcs\Api\Entity\Si\SiCategory as SiCategoryEntity;
use Dvsa\Olcs\Api\Entity\Si\SiCategoryType as SiCategoryTypeEntity;

/**
 * SeriousInfringement Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="serious_infringement",
 *    indexes={
 *        @ORM\Index(name="ix_serious_infringement_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_serious_infringement_si_category_id", columns={"si_category_id"}),
 *        @ORM\Index(name="ix_serious_infringement_si_category_type_id", columns={"si_category_type_id"}),
 *        @ORM\Index(name="ix_serious_infringement_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_serious_infringement_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_serious_infringement_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class SeriousInfringement extends AbstractSeriousInfringement
{
    public function __construct(
        CaseEntity $case,
        \DateTime $checkDate,
        \DateTime $infringementDate,
        SiCategoryEntity $siCategory,
        SiCategoryTypeEntity $siCategoryType
    ) {
        parent::__construct();

        $this->case = $case;
        $this->checkDate = $checkDate;
        $this->infringementDate = $infringementDate;
        $this->siCategory = $siCategory;
        $this->siCategoryType = $siCategoryType;
    }

    /**
     * Whether there is a response set for the serious infringement
     *
     * @return bool
     */
    public function responseSet()
    {
        return (bool)!$this->appliedPenalties->isEmpty();
    }

    /**
     * Calculated values to be added to a bundle
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        return ['responseSet' => $this->responseSet()];
    }
}
