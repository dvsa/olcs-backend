<?php

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessDetails extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';

    protected $organisationRepo;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->organisationRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('Organisation');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchUsingId($query);
        $organisation = $licence->getOrganisation();

        $organisation = $this->organisationRepo->fetchBusinessTypeById($organisation->getId());

        $orgData = $organisation->jsonSerialize();

        $orgData['tradingNames'] = $licence->getTradingNames();
        $orgData['companySubsidiaries'] = $licence->getCompanySubsidiaries();

        return $orgData;
    }
}
