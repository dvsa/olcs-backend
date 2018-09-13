<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
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
class IrhpPermitApplication extends AbstractIrhpPermitApplication
{
    public static function createNew(
        IrhpPermitWindow $IrhpPermitWindow,
        Licence $licence,
        EcmtPermitApplication $ecmtPermitApplication
    ) {
        $IrhpPermitApplication = new self();

        $IrhpPermitApplication->irhpPermitWindow = $IrhpPermitWindow;
        $IrhpPermitApplication->licence = $licence;
        $IrhpPermitApplication->ecmtPermitApplication = $ecmtPermitApplication;

        return $IrhpPermitApplication;
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
        return [
            'successfulPermitApplications' => $this->countSuccessfulPermitApplications()
        ];
    }

    /**
     * Get num of successful permit applications
     **
     * @return int
     */
    public function countSuccessfulPermitApplications()
    {
        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->eq('successful', true)
        );
        $applications = $this->getIrhpCandidatePermits()->matching($criteria);

        return count($applications);
    }
}
