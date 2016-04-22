<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationReadAudit;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationReadAudit as OrganisationReadAuditEntity;
use Dvsa\Olcs\Transfer\Query\Audit\ReadOrganisation;
use Mockery as m;

/**
 * Organisation Read Audit Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationReadAuditTest extends AbstractReadAuditTest
{
    public function setUp()
    {
        $this->setUpSut(OrganisationReadAudit::class, true);
    }

    public function testFetchOne()
    {
        parent::commonTestFetchOne('organisation');
    }

    public function testFetchList()
    {
        parent::commonTestFetchList(
            ReadOrganisation::create(['id' => 111]),
            ' AND m.organisation = [[111]]'
        );
    }

    public function testDeleteOlderThan()
    {
        parent::commonTestDeleteOlderThan(OrganisationReadAuditEntity::class);
    }
}
