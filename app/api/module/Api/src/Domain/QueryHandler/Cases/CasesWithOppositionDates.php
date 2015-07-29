<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CaseEntity;

/**
 * Cases with OOO and OOR dates attached
 */
final class CasesWithOppositionDates extends AbstractQueryHandler
{
    protected $repoServiceName = 'Cases';

    public function handleQuery(QueryInterface $query)
    {
        $case = $this->getRepo()->fetchUsingId($query);

        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->in(
                'publicationSection',
                [
                    $this->getRepo()->getReference(\Dvsa\Olcs\Api\Entity\Publication\PublicationSection::class, 1),
                    $this->getRepo()->getReference(\Dvsa\Olcs\Api\Entity\Publication\PublicationSection::class, 3),
                ]
            )
        );

        // @todo look at simplifying
        return $this->result(
            $case,
            [
                'application' => array(
                    'operatingCentres',
                    'publicationLinks' => array(
                        'criteria' => $criteria,
                        'publication'
                    )
                ),
                'licence' => [
                    'goodsOrPsv'
                ]
            ],
            [
                'oooDate' => $this->calculateOoo($case),
                'oorDate' => $this->calculateOor($case)
            ]
        );
    }

    /**
     * Calculate Out of Representation date
     *
     * @param CaseEntity $case
     *
     * @return string A date string or and empty string
     */
    public function calculateOor(CaseEntity $case)
    {
        if (!empty($case->getApplication())) {
            $oorDate = $case->getApplication()->getOutOfRepresentationDate();
            if ($oorDate instanceof \DateTime) {
                return $oorDate->format(\DateTime::ISO8601);
            }
        }

        return '';
    }

    /**
     * Calculate the Out of Opposition date
     *
     * @param CaseEntity $case
     *
     * @return string A date string or and empty string
     */
    public function calculateOoo(CaseEntity $case)
    {
        if (!empty($case->getApplication())) {
            $oooDate = $case->getApplication()->getOutOfOppositionDate();
            if ($oooDate instanceof \DateTime) {
                return $oooDate->format(\DateTime::ISO8601);
            }
        }
        return '';
    }
}
