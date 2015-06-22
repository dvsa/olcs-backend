<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;

/**
 * Cases Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="cases",
 *    indexes={
 *        @ORM\Index(name="ix_cases_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_cases_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_cases_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_cases_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_cases_case_type", columns={"case_type"}),
 *        @ORM\Index(name="ix_cases_erru_case_type", columns={"erru_case_type"}),
 *        @ORM\Index(name="ix_cases_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_cases_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class Cases extends AbstractCases
{

    /**
     * Creates a new case entity and sets the open date
     *
     * @param \DateTime $openDate
     */
    public function __construct(\DateTime $openDate)
    {
        parent::__construct();

        $this->setOpenDate($openDate);
    }

    /**
     * @param RefData $caseType
     * @param ArrayCollection $categorys
     * @param ArrayCollection $outcomes
     * @param Application|null $application
     * @param Licence|null $licence
     * @param TransportManager|null $transportManager
     * @param string $ecmsNo
     * @param string $description
     * @return bool
     */
    public function update(
        $caseType,
        $categorys,
        $outcomes,
        $application,
        $licence,
        $transportManager,
        $ecmsNo,
        $description
    )
    {
        $this->setCaseType($caseType);
        $this->setCategorys($categorys);
        $this->setOutcomes($outcomes);
        $this->setApplication($application);
        $this->setLicence($licence);
        $this->setTransportManager($transportManager);
        $this->setEcmsNo($ecmsNo);
        $this->setDescription($description);

        return true;
    }

    /**
     * Updates annual test history
     *
     * @param string $annualTestHistory
     * @return bool
     */
    public function updateAnnualTestHistory($annualTestHistory)
    {
        $this->setAnnualTestHistory($annualTestHistory);

        return true;
    }
}
