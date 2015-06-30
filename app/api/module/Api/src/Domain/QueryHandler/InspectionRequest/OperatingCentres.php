<?php

/**
 * Operating Centres for Inspection Request
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\InspectionRequest;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLicRepo;

/**
 * Operating Centres for Inspection Request
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OperatingCentres extends AbstractQueryHandler
{
    protected $repoServiceName = 'LicenceOperatingCentre';

    protected $extraRepos = ['ApplicationOperatingCentre'];

    public function handleQuery(QueryInterface $query)
    {
        die('operating centres');
        
        if ($this->getType() == 'application') {
            $data = $this->getServiceLocator()
                ->get('Entity\ApplicationOperatingCentre')
                ->getForSelect($this->getIdentifier());

            $this->formatted = true;
        } else {
            $dataFetched = $this->getServiceLocator()
                ->get('Entity\LicenceOperatingCentre')
                ->getAllForInspectionRequest($this->getIdentifier());
            $data = isset($dataFetched['Results']) ? $dataFetched['Results'] : null;
        }
        $this->setData('OperatingCentres', $data);
        
        /** @var CommunityLicRepo $repo */
        $repo = $this->getRepo();
        $licence = $this->getRepo('Licence')->fetchById($query->getLicence());

        $officeCopy = $repo->fetchOfficeCopy($query->getLicence());

        return [
            'result' => $repo->fetchList($query),
            'count' =>  $repo->fetchCount($query),
            'totCommunityLicences' => $licence->getTotCommunityLicences(),
            'officeCopy' => $officeCopy
        ];
    }
}
