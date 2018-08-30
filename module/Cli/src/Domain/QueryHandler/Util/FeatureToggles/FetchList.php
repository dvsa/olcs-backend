<?php


namespace Dvsa\Olcs\Cli\Domain\QueryHandler\Util\FeatureToggles;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;


class FetchList extends AbstractListQueryHandler
{
    protected $repoServiceName = 'FeatureToggle';
}
