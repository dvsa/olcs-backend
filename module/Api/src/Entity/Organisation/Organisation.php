<?php

namespace Dvsa\Olcs\Api\Entity\Organisation;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use JsonSerializable;

/**
 * Organisation Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="organisation",
 *    indexes={
 *        @ORM\Index(name="ix_organisation_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_organisation_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_organisation_type", columns={"type"}),
 *        @ORM\Index(name="ix_organisation_lead_tc_area_id", columns={"lead_tc_area_id"}),
 *        @ORM\Index(name="ix_organisation_name", columns={"name"}),
 *        @ORM\Index(name="ix_organisation_contact_details_id", columns={"contact_details_id"}),
 *        @ORM\Index(name="ix_organisation_irfo_contact_details_id", columns={"irfo_contact_details_id"})
 *    }
 * )
 */
class Organisation extends AbstractOrganisation
{
    const ORG_TYPE_PARTNERSHIP = 'org_t_p';
    const ORG_TYPE_OTHER = 'org_t_pa';
    const ORG_TYPE_REGISTERED_COMPANY = 'org_t_rc';
    const ORG_TYPE_LLP = 'org_t_llp';
    const ORG_TYPE_SOLE_TRADER = 'org_t_st';
    const ORG_TYPE_IRFO = 'org_t_ir';

    protected $hasInforceLicences;

    public function hasInforceLicences()
    {
        if ($this->hasInforceLicences === null) {
            $criteria = Criteria::create();
            $criteria->where($criteria->expr()->neq('inForceDate', null));

            $licences = $this->getLicences()->matching($criteria);

            $this->hasInforceLicences = !empty($licences->toArray());
        }

        return $this->hasInforceLicences;
    }

    /**
     * @return array
     * @deprecated
     */
    protected function getCalculatedValues()
    {
        return [
            'hasInforceLicences' => $this->hasInforceLicences(),
            // prevent recursion via app -> licence -> organisation -> licence
            'licences' => null,
        ];
    }

    protected function getCalculatedBundleValues()
    {
        return [
            'hasInforceLicences' => $this->hasInforceLicences()
        ];
    }

    public function updateOrganisation($name, $companyNumber, $firstName, $lastName, $isIrfo)
    {
        if ($isIrfo) {
            $this->isIrfo = 'Y';
        }
        if ($companyNumber) {
            $this->companyOrLLpNo = $companyNumber;
        }
        if (!empty($lastName)) {
            $this->name = trim($firstName . ' ' . $lastName);
        } else {
            $this->name = $name;
        }
    }
}
