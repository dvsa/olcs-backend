<?php

namespace Dvsa\Olcs\Api\Entity\Organisation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\LicenceStatusAwareTrait;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser as OrganisationUserEntity;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Service\Document\ContextProviderInterface;
use Doctrine\ORM\EntityNotFoundException;
use Dvsa\Olcs\Api\Entity\System\RefData;

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
class Organisation extends AbstractOrganisation implements ContextProviderInterface, OrganisationProviderInterface
{
    use LicenceStatusAwareTrait;

    const ORG_TYPE_PARTNERSHIP = 'org_t_p';
    const ORG_TYPE_OTHER = 'org_t_pa';
    const ORG_TYPE_REGISTERED_COMPANY = 'org_t_rc';
    const ORG_TYPE_LLP = 'org_t_llp';
    const ORG_TYPE_SOLE_TRADER = 'org_t_st';
    const ORG_TYPE_IRFO = 'org_t_ir';

    const OPERATOR_CPID_ALL = 'op_cpid_all';

    const ALLOWED_OPERATOR_LOCATION_NI = 'NI';
    const ALLOWED_OPERATOR_LOCATION_GB = 'GB';

    protected $hasInforceLicences;

    /**
     * Has inforce licences
     *
     * @return bool
     */
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
     * Get admin organisation users
     *
     * @return ArrayCollection
     */
    public function getAdminOrganisationUsers()
    {
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->eq('isAdministrator', 'Y')
        );

        /** @var ArrayCollection $adminUsers */
        $adminUsers = $this->getOrganisationUsers()->matching($criteria);

        // unfortunately criteria doesn't work with a relations so need to filter manually
        $enabledOrgUsers = new ArrayCollection();

        /** @var OrganisationUserEntity $orgUser */
        foreach ($adminUsers as $orgUser) {
            try {
                $user = $orgUser->getUser();
                if ($user instanceof UserEntity
                    && $user->getAccountDisabled() !== 'Y') {
                    $enabledOrgUsers->add($orgUser);
                }
            } catch (EntityNotFoundException $ex) {
                // we may have the user id but will not be able to load it
                // because SoftDeleteable is used
            }
        }

        return $enabledOrgUsers;
    }

    /**
     * Whether a user can access permits
     */
    public function isEligibleForPermits()
    {
        $licences = $this->getLicences();

        /**
         * Iterate through the licences, looking for a (valid) standard international goods licence
         * Stop as soon as we find one
         *
         * @var LicenceEntity $licence
         */
        foreach ($licences as $licence) {
            if ($licence->isEligibleForPermits()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Has even one Operator-admin users a corrent email address
     *
     * @return bool
     */
    public function hasAdminEmailAddresses()
    {
        $emailValidator = new \Zend\Validator\EmailAddress;

        /** @var OrganisationUser $orgUser */
        foreach ($this->getAdminOrganisationUsers() as $orgUser) {
            $email = $orgUser->getUser()->getContactDetails()->getEmailAddress();

            if (!empty($email)
                && $emailValidator->isValid($email)) {
                return true;
            }
        }

        return false;
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
     * Is this organisation a Ltd company
     *
     * @return bool
     */
    public function isLtd()
    {
        return $this->getType()->getId() === self::ORG_TYPE_REGISTERED_COMPANY;
    }

    /**
     * @return array
     * @deprecated
     */
    protected function getCalculatedValues()
    {
        return [
            'hasInforceLicences' => $this->hasInforceLicences(),
            /* prevent recursion via app -> licence -> organisation -> licence */
            'licences' => null,
        ];
    }

    /**
     * getCalculatedBundleValues
     *
     * @return array hasInforceLicences
     */
    protected function getCalculatedBundleValues()
    {
        return [
            'hasInforceLicences' => $this->hasInforceLicences(),
            'eligibleEcmtLicences' => $this->getEligibleEcmtLicences()
        ];
    }

    /**
     * Update Organisation
     *
     * @param $name             name
     * @param $companyNumber    company number
     * @param $firstName        first name
     * @param $lastName         last name
     * @param $isIrfo           ir info
     * @param $natureOfBusiness nature of business
     * @param $cpid             cpid
     * @param $allowEmail       allows email
     *
     */
    public function updateOrganisation(
        $name,
        $companyNumber,
        $firstName,
        $lastName,
        $isIrfo,
        $natureOfBusiness,
        $cpid,
        $allowEmail
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

        $this->setAllowEmail($allowEmail);
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
     * Has active licences
     *
     * @param string $goodsOrPsv Check only licences of a given goodsOrPsv value
     *
     * @return bool
     */
    public function hasActiveLicences($goodsOrPsv = null)
    {
        return !$this->getActiveLicences($goodsOrPsv)->isEmpty();
    }

    /**
     * Get active licences
     *
     * @param string $goodsOrPsv Return only licences of a given goodsOrPsv value
     *
     * @return ArrayCollection LicenceEntity[]
     */
    public function getActiveLicences($goodsOrPsv = null)
    {
        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->in(
                'status',
                $this->getLicenceStatusesActive()
            )
        );

        if ($goodsOrPsv !== null) {
            $criteria->andWhere($criteria->expr()->in('goodsOrPsv', [$goodsOrPsv]));
        }

        return $this->getLicences()->matching($criteria);
    }

    /**
     * Get related licences
     *
     * @return ArrayCollection
     */
    public function getRelatedLicences()
    {
        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->in(
                'status',
                [
                    LicenceEntity::LICENCE_STATUS_NOT_SUBMITTED,
                    LicenceEntity::LICENCE_STATUS_UNDER_CONSIDERATION,
                    LicenceEntity::LICENCE_STATUS_GRANTED,
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
     * @param int $licNo Licence Number
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
     * Determine is an organisation isMlh (Multiple Licence Holder has at least two valid Goods licences)
     * Note: Licences are considered valid if in one of the following states:
     *
     *  Under consideration
     *  Granted
     *  Valid
     *  Curtailed
     *  Suspended
     *
     *  Additional A/C:
     *  Where licence status is Under consideration or Granted, pull the operator type from the associated
     *  new application. (There should be only be one associated application)
     *  For the Valid, Curtailed or Suspended statuses, pull the operator type from the licence record
     *
     * @return bool
     */
    public function isMlh()
    {
        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->in(
                'status',
                [
                    LicenceEntity::LICENCE_STATUS_VALID,
                    LicenceEntity::LICENCE_STATUS_CURTAILED,
                    LicenceEntity::LICENCE_STATUS_SUSPENDED
                ]
            )
        );

        // And the licence must be for goods vehicles
        $criteria->andWhere($criteria->expr()->in('goodsOrPsv', [LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE]));
        $totalValidLicences = $this->getLicences()->matching($criteria)->count();

        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->in(
                'status',
                [
                    LicenceEntity::LICENCE_STATUS_UNDER_CONSIDERATION,
                    LicenceEntity::LICENCE_STATUS_GRANTED,
                ]
            )
        );
        /** @var ArrayCollection $newLicences */
        $newLicences = $this->getLicences()->matching($criteria);

        $totalLicencesWithNewGoodsApplications = 0;

        /** @var LicenceEntity $licence */
        foreach ($newLicences as $licence) {
            $applications = $licence->getApplications();
            if ($applications->count() > 0 && $applications->first()->isGoods()) {
                $totalLicencesWithNewGoodsApplications++;
            }
        }

        return (bool)(($totalValidLicences + $totalLicencesWithNewGoodsApplications) > 1);
    }

    /**
     * Get All Outstanding applications for all licences
     * Status "under consideration" or "granted" and optionally "not submitted"
     *
     * @param bool $includeNotSubmitted Is include Not Submitted
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOutstandingApplications($includeNotSubmitted = false)
    {
        $applications = [];

        $licences = $this->getLicences();

        /** @var LicenceEntity $licence */
        foreach ($licences as $licence) {
            $outstandingApplications = $licence->getOutstandingApplications($includeNotSubmitted)->toArray();
            $applications = array_merge($applications, $outstandingApplications);
        }
        return new ArrayCollection($applications);
    }

    /**
     * Returns licences linked to this organisation for submissions
     * NOTE: In submissions, the licence the submission relates to is ALSO EXCLUDED. As these are 'linked licences'.
     *
     * @return ArrayCollection
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

    public function getContextValue()
    {
        return $this->getId();
    }

    /**
     * @inheritdoc
     */
    public function getRelatedOrganisation()
    {
        return $this;
    }

    /**
     * Gets email addresses for organisation administrator users
     *
     * @return array
     */
    public function getAdminEmailAddresses()
    {
        $adminUsers = $this->getAdministratorUsers();
        $adminEmails = [];

        /** @var OrganisationUserEntity $orgUser */
        foreach ($adminUsers as $orgUser) {
            try {
                $orgUser = $orgUser->getUser();
            } catch (EntityNotFoundException $ex) {
                //soft delete means no organisation user
                continue;
            }
            $adminEmails[] = $orgUser->getContactDetails()->getEmailAddress();
        }

        return $adminEmails;
    }

    /**
     * Get Administrator Users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdministratorUsers()
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();

        $criteria->where($expr->eq('isAdministrator', 'Y'));

        return $this->organisationUsers->matching($criteria);
    }

    /**
     * Returns the latest Trading Name that hasnt been deleted
     *
     * @return string
     */
    public function getTradingAs()
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->andWhere($expr->isNull('deletedDate'))
            ->orderBy(array('createdOn' => Criteria::DESC))
            ->setMaxResults(1);
        $matches = $this->tradingNames->matching($criteria);
        if (!empty($matches)) {
            return $matches[0]->getName();
        }
        return '';
    }

    /**
     * Get allowed operator location
     *
     * @return string|null
     */
    public function getAllowedOperatorLocation()
    {
        $allowedOperatorLocation = null;
        $outstandingApplications = $this->getOutstandingApplications(true);
        if ($outstandingApplications->count() && $outstandingApplications[0]->getNiFlag() !== null) {
            $allowedOperatorLocation = $outstandingApplications[0]->getNiFlag() === 'Y' ?
                self::ALLOWED_OPERATOR_LOCATION_NI : self::ALLOWED_OPERATOR_LOCATION_GB;
        } else {
            $licences = $this->getLicences();
            $trafficAreaCode = null;
            /** @var LicenceEntity $licence */
            foreach ($licences as $licence) {
                if ($licence->getStatus() &&
                    ($licence->getStatus()->getId() === LicenceEntity::LICENCE_STATUS_CANCELLED ||
                        $licence->getStatus()->getId() === LicenceEntity::LICENCE_STATUS_WITHDRAWN)
                ) {
                    continue;
                }
                if ($licence->getTrafficArea() !== null) {
                    $allowedOperatorLocation =
                        $licence->getTrafficArea()->getId() ===
                        TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE ?
                            self::ALLOWED_OPERATOR_LOCATION_NI : self::ALLOWED_OPERATOR_LOCATION_GB;
                    break;
                }
            }
        }

        return $allowedOperatorLocation;
    }

    /**
     * Has unlicenced licences
     *
     * @return bool
     */
    public function hasUnlicencedLicences()
    {
        $criteria = Criteria::create();
        $criteria->where($criteria->expr()->contains('licNo', 'U'));

        return !empty($this->getLicences()->matching($criteria)->toArray());
    }

    /**
     * Get eligible ECMT licences
     **
     * @return array
     */
    public function getEligibleEcmtLicences()
    {
        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->in(
                'status',
                [
                    LicenceEntity::LICENCE_STATUS_VALID,
                    LicenceEntity::LICENCE_STATUS_SUSPENDED,
                    LicenceEntity::LICENCE_STATUS_CURTAILED
                ]
            )
        );
        $criteria->andWhere(
            $criteria->expr()->in(
                'goodsOrPsv',
                [
                    LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE
                ]
            )
        );
        $criteria->andWhere(
            $criteria->expr()->in(
                'licenceType',
                [
                    LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                    LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
                    LicenceEntity::LICENCE_TYPE_RESTRICTED
                ]
            )
        );

        $licences = $this->getLicences()->matching($criteria);

        $licencesArr = [];

        /** @var LicenceEntity $licence */
        foreach ($licences as $licence) {
            $licencesArr[] = [
                'id' => $licence->getId(),
                'licNo' => $licence->getLicNo(),
                'trafficArea' => $licence->getTrafficArea()->getName(),
                'totAuthVehicles' => $licence->getTotAuthVehicles(),
                'licenceType' => $licence->getLicenceType(),
                'hasActiveEcmtApplication' => $licence->hasActiveEcmtApplication()
            ];
        }

        return [
            'result' => $licencesArr
        ];
    }
}
