<?php

/**
 * Organisation for Permits
 *
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Organisation;

use DateTime;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class OrganisationPermits extends AbstractQueryHandler
{
    protected $repoServiceName = 'Organisation';

    protected $extraRepos = ['IrhpPermitWindow'];

    public function handleQuery(QueryInterface $query)
    {
        /* @var $organisation \Dvsa\Olcs\Api\Entity\Organisation\Organisation */
        $organisation = $this->getRepo()->fetchUsingId($query);

        $values = [];

        try {
            $window = $this->getRepo('IrhpPermitWindow')->fetchLastOpenWindowByIrhpPermitType(
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
                new DateTime(),
                Query::HYDRATE_OBJECT,
                $query->getYear()
            );

            $values['eligibleLicences'] = $organisation->getEligibleLicences($window->getIrhpPermitStock());
        } catch (NotFoundException $e) {
            // still return result even if no window is currently open
        }

        return $this->result($organisation, [], $values);
    }
}
