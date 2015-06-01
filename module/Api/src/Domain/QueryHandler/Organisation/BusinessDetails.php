<?php

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Organisation;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrgEntity;
use Dvsa\Olcs\Transfer\Query\Organisation\BusinessDetails as Qry;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessDetails extends AbstractQueryHandler
{
    protected $repoServiceName = 'Organisation';

    public function handleQuery(QueryInterface $query)
    {
        /** @var OrgEntity $organisation */
        $organisation = $this->getRepo()->fetchBusinessTypeUsingId($query);

        $orgData = $organisation->jsonSerialize();

        $licence = $this->getLicenceFilter($query);

        if ($licence !== null) {

            $criteria = Criteria::create();
            $criteria->where(
                $criteria->expr()->eq('licence', $licence)
            );

            $orgData['tradingNames'] = $organisation->getTradingNames()->matching($criteria);
        }

        return $orgData;
    }

    private function getLicenceFilter(Qry $query)
    {
        if ($query->getLicence() !== null) {
            return $query->getLicence();
        }

        if ($query->getApplication() !== null) {
            $application = $this->getRepo()->getReference(Application::class, $query->getApplication());
            return $application->getLicence()->getId();
        }

        return null;
    }
}
