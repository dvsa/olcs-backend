<?php

namespace Dvsa\Olcs\Api\Entity\Organisation;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Service\Document\ContextProviderInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

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
class Organisation extends AbstractOrganisation implements ContextProviderInterface
{
    const ORG_TYPE_PARTNERSHIP = 'org_t_p';
    const ORG_TYPE_OTHER = 'org_t_pa';
    const ORG_TYPE_REGISTERED_COMPANY = 'org_t_rc';
    const ORG_TYPE_LLP = 'org_t_llp';
    const ORG_TYPE_SOLE_TRADER = 'org_t_st';
    const ORG_TYPE_IRFO = 'org_t_ir';

    const OPERATOR_CPID_ALL = 'op_cpid_all';

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

    public function getAdminOrganisationUsers()
    {
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->eq('isAdministrator', 'Y')
        );

        return $this->getOrganisationUsers()->matching($criteria);
    }

    /**
     * Is this organisation a sole trader
     *
     * @return bool
     */
    public function isSoleTrader()
    {
        return $this->getType()->getId() === self::ORG_TYPE_SOLE_TRADER;
    }

    /**
     * Is this organisation a partnership
     *
     * @return bool
     */
    public function isPartnership()
    {
        return $this->getType()->getId() === self::ORG_TYPE_PARTNERSHIP;
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

    public function updateOrganisation(
        $name,
        $companyNumber,
        $firstName,
        $lastName,
        $isIrfo,
        $natureOfBusiness,
        $cpid
    ) {
        $this->setCpid($cpid);
        $this->setNatureOfBusiness($natureOfBusiness);
        if ($isIrfo === 'Y' || ($this->getType() !== null && $this->getType()->getId() === self::ORG_TYPE_IRFO)) {
            $this->isIrfo = 'Y';
        } else {
            $this->isIrfo = 'N';
        }
        if ($companyNumber) {
            $this->companyOrLlpNo = $companyNumber;
        }
        if (!empty($lastName)) {
            $this->name = trim($firstName . ' ' . $lastName);
        } else {
            $this->name = $name;
        }
    }

    /**
     * Is this organisation an unlicensed operator
     *
     * @return bool
     */
    public function isUnlicensed()
    {
        return (boolean)$this->getIsUnlicensed();
    }

    /**
     * @return array LicenceEntity[]
     */
    public function getActiveLicences()
    {
        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->in(
                'status',
                [
                    LicenceEntity::LICENCE_STATUS_VALID,
                    LicenceEntity::LICENCE_STATUS_SUSPENDED,
                    LicenceEntity::LICENCE_STATUS_CURTAILED,
                ]
            )
        );

        return $this->getLicences()->matching($criteria);
    }

    /**
     * Gets a licence from the organisation licences. Used by EBSR to check the licence is related to the organisation,
     * we return more than just a true/false, as the status is checked afterwards
     *
     * @return ArrayCollection
     */
    public function getLicenceByLicNo($licNo)
    {
        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->eq('licNo', $licNo)
        );

        return $this->getLicences()->matching($criteria);
    }

    /**
     * Get the disqualification linked to this organisation
     * NB DB schema is 1 to many, but it is only possible to have one disqualification record per organisation
     *
     * @return null|Disqualification
     */
    public function getDisqualification()
    {
        if ($this->getDisqualifications()->isEmpty()) {
            return null;
        }

        return $this->getDisqualifications()->first();
    }

    /**
     * Get the disqualification status
     *
     * @return string Disqualification constant STATUS_NONE, STATUS_ACTIVE or STATUS_INACTIVE
     */
    public function getDisqualificationStatus()
    {
        if ($this->getDisqualification() === null) {
            return Disqualification::STATUS_NONE;
        }

        return $this->getDisqualification()->getStatus();
    }

    /**
     * Determine is an organisation isMlh (has at least one valid licence)
     *
     * @param $id
     * @return bool
     */
    public function isMlh()
    {
        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->in(
                'status',
                [
                    LicenceEntity::LICENCE_STATUS_VALID
                ]
            )
        );

        return (bool) count($this->getLicences()->matching($criteria));
    }

    /**
     * Get All Outstanding applications for all licences
     * Status "under consideration" or "granted"
     *
     * @return \Doctrine\Common\Collections\Collection|static
     */
    public function getOutstandingApplications()
    {
        $applications = [];

        $licences = $this->getLicences();

        /** @var LicenceEntity $licence */
        foreach ($licences as $licence) {
            $outstandingApplications = $licence->getOutstandingApplications()->toArray();

            $applications += $outstandingApplications;
        }
        return new ArrayCollection($applications);
    }

    /**
     * Returns licences linked to this organisation for submissions
     * NOTE: In submissions, the licence the submission relates to is ALSO EXCLUDED. As these are 'linked licences'.
     * @return array LicenceEntity[]
     */
    public function getLinkedLicences()
    {
        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->notIn(
                'status',
                [
                    LicenceEntity::LICENCE_STATUS_NOT_SUBMITTED,
                    LicenceEntity::LICENCE_STATUS_UNDER_CONSIDERATION,
                    LicenceEntity::LICENCE_STATUS_GRANTED,
                    LicenceEntity::LICENCE_STATUS_WITHDRAWN,
                    LicenceEntity::LICENCE_STATUS_REFUSED
                ]
            )
        );

        return $this->getLicences()->matching($criteria);
    }

    public function getAdminEmailAddresses()
    {
        $users = [];

        /** @var OrganisationUser $orgUser */
        foreach ($this->getAdminOrganisationUsers() as $orgUser) {
            if ($orgUser->getUser()->getContactDetails()->getEmailAddress() !== null) {
                $users[] = $orgUser->getUser()->getContactDetails()->getEmailAddress();
            }
        }
        return $users;
    }

    public function getContextValue()
    {
        return $this->getId();
    }
}
