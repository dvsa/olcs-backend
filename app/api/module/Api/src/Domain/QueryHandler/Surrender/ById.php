<?php


namespace Dvsa\Olcs\Api\Domain\QueryHandler\Surrender;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryByIdHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

final class ById extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_SURRENDER];
    protected $repoServiceName = 'Surrender';

    public function handleQuery(QueryInterface $query)
    {
        $surrender = $this->getRepo('Surrender')->fetchById($query->getId());
        $disableSignatures = $this->getRepo('SystemParameter')->getDisableGdsVerifySignatures();
        return $this->result($surrender, ['disableSignatures'=>$disableSignatures]);
    }
}
