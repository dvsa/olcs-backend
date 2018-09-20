<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
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
            'permitsAwarded' => $this->countPermitsAwarded()
        ];
    }

    /**
     * Get num of successful permit applications
     **
     * @return int
     */
    public function countPermitsAwarded()
    {
        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->eq('successful', true)
        );
        $applications = $this->getIrhpCandidatePermits()->matching($criteria);

        return count($applications);
    }

    /**
     * Method that collects data from given applications
     * for use in deviation calculations
     *
     * @param irhpPermitApplications list of irhp permit applications to collate information from
     *
     * @return array containing data relevant to Deviation calculations as well as the Mean Deviation
     */
    public static function getDeviationData(array $irhpPermitApplications)
    {
        $licence = [];
        $totalPermitsCount = 0;
        $i = 0;
        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $totalPermitsCount += $irhpPermitApplication->getPermitsRequired();
            $licence[$irhpPermitApplication->getLicence()->getLicNo()][$i] = $irhpPermitApplication->getPermitsRequired();
            $i++;
        }

        return [
                'licenceData' => $licence,
                'meanDeviation' => count($licence) / $totalPermitsCount,
            ];
    }


    public function calculateRandomisedScore(array $deviationData)
    {
        $standardDeviation = count($deviationData['licenceData'][$this->getLicence()->getLicNo()]);
        return stats_rand_gen_normal($deviationData['meanDeviation'], $standardDeviation);
    }
}
