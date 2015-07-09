<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Doctrine\Common\Collections\ArrayCollection;

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
    const LICENCE_CASE_TYPE = 'case_t_lic';
    const IMPOUNDING_CASE_TYPE = 'case_t_imp';
    const APP_CASE_TYPE = 'case_t_app';
    const TM_CASE_TYPE = 'case_t_tm';

    /**
     * Creates a new case entity and sets the open date
     *
     * @param \DateTime $openDate
     * @param RefData $caseType
     * @param ArrayCollection $categorys
     * @param ArrayCollection $outcomes
     * @param RefData|null $application
     * @param RefData|null $licence
     * @param RefData|null $transportManager
     * @param string $ecmsNo
     * @param string $description
     */
    public function __construct(
        \DateTime $openDate,
        RefData $caseType,
        ArrayCollection $categorys,
        ArrayCollection $outcomes,
        $application,
        $licence,
        $transportManager,
        $ecmsNo,
        $description
    ) {
        parent::__construct();

        $this->setOpenDate($openDate);
        $this->setCaseType($caseType);
        $this->setCategorys($categorys);
        $this->setOutcomes($outcomes);
        $this->setApplication($application);
        $this->setLicence($licence);
        $this->setTransportManager($transportManager);
        $this->setEcmsNo($ecmsNo);
        $this->setDescription($description);
    }

    /**
     * @param RefData $caseType
     * @param ArrayCollection $categorys
     * @param ArrayCollection $outcomes
     * @param string $ecmsNo
     * @param string $description
     * @return bool
     */
    public function update(
        $caseType,
        $categorys,
        $outcomes,
        $ecmsNo,
        $description
    ) {
        $caseTypeModifyAllowed = [self::IMPOUNDING_CASE_TYPE, self::LICENCE_CASE_TYPE];

        //if case type is allowed to be modified, make sure it's modified to something allowable
        if (in_array($this->caseType, $caseTypeModifyAllowed) && in_array($caseType, $caseTypeModifyAllowed)) {
            $this->setCaseType($caseType);
        }

        $this->setCaseType($caseType);
        $this->setCategorys($categorys);
        $this->setOutcomes($outcomes);
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

    /**
     * Updates conviction note
     *
     * @param string $convictionNote
     * @return bool
     */
    public function updateConvictionNote($convictionNote)
    {
        $this->setConvictionNote($convictionNote);

        return true;
    }

    /**
     * Updates prohibition note
     *
     * @param string $prohibitionNote
     * @return bool
     */
    public function updateProhibitionNote($prohibitionNote)
    {
        $this->setProhibitionNote($prohibitionNote);

        return true;
    }

    /**
     * Checks a stay type exists
     * @param RefData $stayType
     * @return bool
     */
    public function hasStayType(RefData $stayType)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("stayType", $stayType))
            ->setFirstResult(0)
            ->setMaxResults(1);

        $stays = $this->getStays()->matching($criteria);

        return !($stays->isEmpty());
    }

    /**
     * Checks whether an appeal exists
     * @return bool
     */
    public function hasAppeal()
    {
        return !(empty($this->getAppeal()));
    }

    /**
     * @return boolean
     */
    public function isOpen()
    {
        return (
            is_null($this->getClosedDate())
            && is_null($this->getDeletedDate())
        );
    }

    /**
     * @return boolean
     */
    public function hasComplaints()
    {
        return !$this->getComplaints()->isEmpty();
    }
}
