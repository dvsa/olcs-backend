<?php

/**
 * Licence History
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence;

/**
 * Licence History
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceHistory extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    public function handleQuery(QueryInterface $query)
    {
        $applicationRepo = $this->getRepo();
        $application = $applicationRepo->fetchUsingId($query, Query::HYDRATE_OBJECT);
        $data = $application->jsonSerialize();

        $types = [
            'current'        => OtherLicence::TYPE_CURRENT,
            'applied'        => OtherLicence::TYPE_APPLIED,
            'refused'        => OtherLicence::TYPE_REFUSED,
            'revoked'        => OtherLicence::TYPE_REVOKED,
            'public-inquiry' => OtherLicence::TYPE_PUBLIC_INQUIRY,
            'disqualified'   => OtherLicence::TYPE_DISQUALIFIED,
            'held'           => OtherLicence::TYPE_HELD
        ];

        foreach ($types as $key => $type) {
            $otherLicences = $application->getOtherLicencesByType(
                $this->getRepo()->getRefdataReference($type)
            );
            $data['otherLicences'][$key] = $otherLicences->toArray();
        }

        return $data;
    }
}
