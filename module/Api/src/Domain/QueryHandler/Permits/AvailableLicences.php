<?php

/**
 * Retrieve ECMT application record alongside available licences
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\AvailableLicences as AvailableLicencesQry;
use Dvsa\Olcs\Transfer\Query\Organisation\OrganisationAvailableLicences as OrganisationAvailableLicencesQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class AvailableLicences extends AbstractQueryHandler
{
    protected $repoServiceName = 'EcmtPermitApplication';

    public function handleQuery(QueryInterface $query)
    {
        /**
         * @var EcmtPermitApplicationRepo   $ecmtPermitApplicationRepo
         * @var EcmtPermitApplicationEntity $ecmtPermitApplication
         * @var AvailableLicencesQry        $query
         */
        $ecmtPermitApplicationRepo = $this->getRepo();
        $ecmtPermitApplication = $ecmtPermitApplicationRepo->fetchUsingId($query);

        $queryData = [
            'id' => $this->getCurrentOrganisation()->getId(),
            'irhpPermitType' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
            'irhpPermitStock' => $ecmtPermitApplication->getAssociatedStock()->getId(),
        ];

        $availableLicencesQry = OrganisationAvailableLicencesQry::create($queryData);
        $availableLicences = $this->getQueryHandler()->handleQuery($availableLicencesQry);

        $availableLicences['selectedLicence'] = $ecmtPermitApplication->getLicence()->getId();
        $availableLicences['isNotYetSubmitted'] = $ecmtPermitApplication->isNotYetSubmitted();

        return $availableLicences;
    }
}
