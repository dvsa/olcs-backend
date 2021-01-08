<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\CommunityLic;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicenceEntity;
use Mockery as m;

class CommunityLicenceRepositoryMockBuilder extends RepositoryMockBuilder
{
    const ALIAS = 'CommunityLic';

    public function __construct()
    {
        parent::__construct(CommunityLic::class);
    }

    /**
     * @inheritDoc
     */
    protected function buildDefaultEntity($id)
    {
        $entity = m::mock(CommunityLicenceEntity::class);
        $entity->shouldReceive('getId')->andReturn($id)->byDefault();
        $entity->shouldReceive('getCalculatedBundleValues')->andReturn([])->byDefault();
        $entity->shouldReceive('serialize')->andReturn([])->byDefault();
        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function build()
    {
        $mock = parent::build();
        $mock->shouldReceive('fetchOfficeCopy')->andReturnUsing(function () {
            return $this->buildEntity(1);
        })->byDefault();
        $mock->shouldReceive('fetchList')->andReturn([])->byDefault();
        $mock->shouldReceive('fetchCount')->andReturn(0)->byDefault();
        $mock->shouldReceive('hasRows')->andReturn(true)->byDefault();
        $mock->shouldReceive('countActiveByLicenceId')->andReturn(0)->byDefault();
        return $mock;
    }
}
