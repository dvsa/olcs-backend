<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;

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
}
