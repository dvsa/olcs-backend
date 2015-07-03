<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Util\DateTime\AddDays;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Publication\Publication;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
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
            $criteria->expr()->in('publicationSection', [1, 3])
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
                )
            ],
            [
                'oooDate' => $this->calculateOoo($case),
                'oorDate' => $this->calculateOor($case)
            ]
        );
    }

    /**
     * Calculate Out of representation date
     *
     * @param CaseEntity $case
     * @return array
     */
    public function calculateOor(CaseEntity $case)
    {
        if (!empty($case->getApplication())) {
            $operatingCentres = $case->getApplication()->getOperatingCentres()->toArray();
            if (isset($operatingCentres[0]) && !empty($operatingCentres[0]->getAdPlacedDate())) {
                usort(
                    $operatingCentres,
                    function ($a, $b) {
                        return strtotime($b->getAdPlacedDate()) - strtotime($a->getAdPlacedDate());
                    }
                );

                if (!empty($operatingCentres[0]->getAdPlacedDate())) {
                    $newsDateObj = new \DateTime($operatingCentres[0]->getAdPlacedDate());
                    $addDays = new AddDays();
                    $oorDate = $addDays->calculateDate($newsDateObj, 21);

                    return !empty($oorDate) ? $oorDate->format(\DateTime::ISO8601) : '';
                }
            }
        }

        return '';
    }

    /**
     * Calculate the Out of Representation date
     *
     * @param CaseEntity $case
     * @return string
     */
    public function calculateOoo(CaseEntity $case)
    {
        if (!empty($case->getApplication())) {
            /** @var Publication $latestPublication */
            $latestPublication = $this->getLatestPublication($case->getApplication());

            if (!empty($latestPublication->getPubDate())) {
                $pubDateObj = new \DateTime($latestPublication->getPubDate());

                $addDays = new AddDays();

                $oooDate = $addDays->calculateDate($pubDateObj, 21);

                return !empty($oooDate) ? $oooDate->format(\DateTime::ISO8601) : '';
            }
        }
        return '';
    }

    /**
     * Gets the latest publication for an application. (used to calculate OOO date)
     *
     * @param Application $application
     * @return array|null
     */
    private function getLatestPublication(Application $application)
    {
        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->in('publicationSection', [1, 3])
        );
        $publicationLinks = $application->getPublicationLinks()->matching($criteria);
        if (!empty($publicationLinks)
        ) {
            $publications = array();
            /** @var PublicationLink $pub */
            foreach ($publicationLinks as $pub) {
                $publicationSectionId = $pub->getPublicationSection()->getId();
                if (in_array($publicationSectionId, [1,3])) {
                    array_push($publications, $pub->getPublication());
                }
            }
            usort(
                $publications,
                function ($a, $b) {
                    return strtotime($b->getPubDate()) - strtotime($a->getPubDate());
                }
            );
            return $publications[0];
        }
        return null;
    }
}
