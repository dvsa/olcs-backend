<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Query\Permits\EcmtCountriesList as ListDto;


/**
 * Get a list of ECMT Countries
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class EcmtCountriesList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Country';

    /**
     * @var ListDto
     */
    private $listDto;

    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();
        $data = $query->getArrayCopy();
        $this->listDto = ListDto::create($data);
        $results = $repo->fetchList($this->listDto, Query::HYDRATE_OBJECT);

        return [
          'results' => $this->resultList(
            $results
          ),
          'count' => $repo->fetchCount($this->listDto)
        ];
    }

    /**
     * Gets the list Dto that was used (gets round problem with UT comparing objects)
     *
     * @return ListDto
     */
    public function getListDto()
    {
        return $this->listDto;
    }
}
