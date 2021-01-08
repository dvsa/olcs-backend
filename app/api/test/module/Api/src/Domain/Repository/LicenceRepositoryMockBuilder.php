<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;

class LicenceRepositoryMockBuilder extends RepositoryMockBuilder
{
    const ALIAS = 'Licence';

    public function __construct()
    {
        parent::__construct(LicenceRepo::class);
    }

    /**
     * @inheritDoc
     */
    protected function buildDefaultEntity($id)
    {
        $organisation = new Organisation();
        $status = new RefData(Licence::LICENCE_STATUS_VALID);
        $entity = new Licence($organisation, $status);

        $ta = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $ta->setId('T');
        $entity->setTrafficArea($ta);

        $entity->setId($id);

        return $entity;
    }
}
