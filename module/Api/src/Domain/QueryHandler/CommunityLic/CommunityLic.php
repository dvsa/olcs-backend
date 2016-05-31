<?php

/**
 * Community Licence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLic as CommunityLicRepo;
use Doctrine\ORM\Query;

/**
 * Community Licence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLic extends AbstractQueryHandler
{
    protected $repoServiceName = 'CommunityLic';

    protected $extraRepos = ['Licence'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var CommunityLicRepo $repo */
        $repo = $this->getRepo();
        $licence = $this->getRepo('Licence')->fetchById($query->getLicence());

        $officeCopy = $repo->fetchOfficeCopy($query->getLicence());

        $data = $query->getArrayCopy();

        unset($data['statuses']);

        $unfilteredQuery = \Dvsa\Olcs\Transfer\Query\CommunityLic\CommunityLic::create($data);

        return [
            'result' => $this->resultList(
                $repo->fetchList($query, Query::HYDRATE_OBJECT)
            ),
            'count' =>  $repo->fetchCount($query),
            'count-unfiltered' => $repo->hasRows($unfilteredQuery),
            'totCommunityLicences' => $licence->getTotCommunityLicences(),
            'officeCopy' => $this->result($officeCopy)
        ];
    }
}
