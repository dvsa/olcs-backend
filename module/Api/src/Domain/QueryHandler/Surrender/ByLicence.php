<?php


namespace Dvsa\Olcs\Api\Domain\QueryHandler\Surrender;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

final class ByLicence extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_SURRENDER];
    protected $repoServiceName = 'Surrender';
    protected $extraRepos = ['SystemParameter'];

    /**
     * handleQuery
     *
     * @param QueryInterface $query
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        $licenceId = $query->getId();
        $surrender = $this->getRepo('Surrender')->fetchOneByLicence($licenceId, Query::HYDRATE_OBJECT);
        return $this->result(
            $surrender,
            ['licence', 'status', 'licenceDocumentStatus', 'communityLicenceDocumentStatus', 'digitalSignature'],
            [
                'disableSignatures' => $this->getRepo('SystemParameter')->getDisableGdsVerifySignatures()
            ]
        );
    }
}
