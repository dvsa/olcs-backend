<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use DateTime;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\Traits\TieredProductReference;

/**
 * IrhpPermitApplication Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit_application",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_applications_irhp_permit_windows1_idx",
     *     columns={"irhp_permit_window_id"}),
 *        @ORM\Index(name="fk_irhp_permit_applications_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_irhp_permit_application_ecmt_permit_application1_idx",
     *     columns={"ecmt_permit_application_id"}),
 *        @ORM\Index(name="fk_irhp_permit_application_sectors_id1_idx", columns={"sectors_id"}),
 *        @ORM\Index(name="irhp_permit_type_ref_data_status_id_fk", columns={"status"}),
 *        @ORM\Index(name="fk_irhp_permit_application_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_permit_application_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class IrhpPermitApplication extends AbstractIrhpPermitApplication implements OrganisationProviderInterface
{
    use TieredProductReference;

    const MULTILATERAL_ISSUE_FEE_PRODUCT_REFERENCE_MONTH_ARRAY = [
        'Jan' => FeeType::FEE_TYPE_IRHP_ISSUE_MULTILATERAL_PRODUCT_REF,
        'Feb' => FeeType::FEE_TYPE_IRHP_ISSUE_MULTILATERAL_PRODUCT_REF,
        'Mar' => FeeType::FEE_TYPE_IRHP_ISSUE_MULTILATERAL_PRODUCT_REF,
        'Apr' => FeeType::FEE_TYPE_IRHP_MULTI_ISSUE_75_PRODUCT_REF,
        'May' => FeeType::FEE_TYPE_IRHP_MULTI_ISSUE_75_PRODUCT_REF,
        'Jun' => FeeType::FEE_TYPE_IRHP_MULTI_ISSUE_75_PRODUCT_REF,
        'Jul' => FeeType::FEE_TYPE_IRHP_MULTI_ISSUE_50_PRODUCT_REF,
        'Aug' => FeeType::FEE_TYPE_IRHP_MULTI_ISSUE_50_PRODUCT_REF,
        'Sep' => FeeType::FEE_TYPE_IRHP_MULTI_ISSUE_50_PRODUCT_REF,
        'Oct' => FeeType::FEE_TYPE_IRHP_MULTI_ISSUE_25_PRODUCT_REF,
        'Nov' => FeeType::FEE_TYPE_IRHP_MULTI_ISSUE_25_PRODUCT_REF,
        'Dec' => FeeType::FEE_TYPE_IRHP_MULTI_ISSUE_25_PRODUCT_REF,
    ];

    public static function createNew(
        IrhpPermitWindow $IrhpPermitWindow,
        Licence $licence,
        EcmtPermitApplication $ecmtPermitApplication = null,
        IrhpApplication $irhpApplication = null
    ) {
        $IrhpPermitApplication = new self();

        $IrhpPermitApplication->irhpPermitWindow = $IrhpPermitWindow;
        $IrhpPermitApplication->licence = $licence;
        $IrhpPermitApplication->ecmtPermitApplication = $ecmtPermitApplication;
        $IrhpPermitApplication->irhpApplication = $irhpApplication;

        return $IrhpPermitApplication;
    }

    /**
     * createNewForIrhpApplication
     *
     * @param IrhpApplication  $irhpApplication  IRHP Application
     * @param IrhpPermitWindow $irhpPermitWindow IRHP Permit Window
     *
     * @return IrhpPermitApplication
     */
    public static function createNewForIrhpApplication(
        IrhpApplication $irhpApplication,
        IrhpPermitWindow $irhpPermitWindow
    ) {
        $irhpPermitApplication = new self();
        $irhpPermitApplication->irhpApplication = $irhpApplication;
        $irhpPermitApplication->irhpPermitWindow = $irhpPermitWindow;

        return $irhpPermitApplication;
    }

    public function getPermitIntensityOfUse()
    {
        return $this->ecmtPermitApplication->getPermitIntensityOfUse();
    }

    public function getPermitApplicationScore()
    {
        return $this->ecmtPermitApplication->getPermitApplicationScore();
    }

    /**
     * getCalculatedBundleValues
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        $relatedApplication = $this->getRelatedApplication();

        return [
            'permitsAwarded' => $this->countPermitsAwarded(),
            'validPermits' => $this->countValidPermits(),
            'relatedApplication' => isset($relatedApplication) ? $relatedApplication->serialize(
                [
                    'licence' => [
                        'organisation'
                    ]
                ]
            ) : null,
        ];
    }

    /**
     * Get num of successful permit applications
     *
     * @return int
     */
    public function countPermitsAwarded()
    {
        return count($this->getSuccessfulIrhpCandidatePermits());
    }

    /**
     * Get num of valid permits
     *
     * @return int
     */
    public function countValidPermits()
    {
        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->notIn('status', IrhpPermit::$nonValidStatuses)
        );
        $permits = $this->getIrhpPermits()->matching($criteria);

        return $permits->count();
    }

    /**
     * Get candidate permits marked as successful
     *
     * @return array
     */
    public function getSuccessfulIrhpCandidatePermits()
    {
        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->eq('successful', true)
        );

        return $this->getIrhpCandidatePermits()->matching($criteria);
    }

    /**
     * Has permits required populated
     *
     * @return bool
     */
    public function hasPermitsRequired()
    {
        return $this->permitsRequired !== null;
    }

    /**
     * Sets the permits required within the stock associated with this entity
     *
     * @param int $permitsRequired
     */
    public function updatePermitsRequired($permitsRequired)
    {
        if (!is_null($this->irhpApplication) && $this->irhpApplication->canBeUpdated()) {
            $this->permitsRequired = $permitsRequired;
        }
    }

    /**
     * Get related organisation
     *
     * @return Organisation|null
     */
    public function getRelatedOrganisation()
    {
        $relatedApplication = $this->getRelatedApplication();

        return isset($relatedApplication) ? $relatedApplication->getRelatedOrganisation() : null;
    }

    /**
     * There are two possible types of parent application ECMT or IRHP
     *
     * @return EcmtPermitApplication|IrhpApplication|null
     */
    public function getRelatedApplication()
    {
        if ($this->ecmtPermitApplication instanceof EcmtPermitApplication) {
            return $this->ecmtPermitApplication;
        } elseif ($this->irhpApplication instanceof IrhpApplication) {
            return $this->irhpApplication;
        }

        return null;
    }

    /**
     * Has valid permits
     *
     * @return bool
     */
    public function hasValidPermits()
    {
        return $this->countValidPermits() > 0;
    }

    /**
     * Returns the issue fee product reference for this application
     * Applicable to bilateral and multilateral only
     *
     * @param DateTime $dateTime (optional)
     *
     * @return string
     *
     * @throws ForbiddenException
     */
    public function getIssueFeeProductReference(?DateTime $dateTime = null)
    {
        if (is_null($dateTime)) {
            $dateTime = new DateTime();
        }

        $irhpPermitTypeId = $this->getIrhpApplication()->getIrhpPermitType()->getId();
        switch ($irhpPermitTypeId) {
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL:
                $productReference = FeeType::FEE_TYPE_IRHP_ISSUE_BILATERAL_PRODUCT_REF;
                break;
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL:
                $irhpPermitStock = $this->getIrhpPermitWindow()->getIrhpPermitStock();
                $productReference = $this->genericGetProdRefForTier(
                    $irhpPermitStock->getValidFrom(true),
                    $irhpPermitStock->getValidTo(true),
                    $dateTime,
                    self::MULTILATERAL_ISSUE_FEE_PRODUCT_REFERENCE_MONTH_ARRAY
                );
                break;
            default:
                throw new ForbiddenException(
                    'Cannot get issue fee product ref and quantity for irhp permit type ' . $irhpPermitTypeId
                );
        }

        return $productReference;
    }

    /**
     * Clear permits required
     *
     * @return bool
     */
    public function clearPermitsRequired()
    {
        return $this->permitsRequired = null;
    }
}
