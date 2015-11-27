<?php

namespace Dvsa\OlcsTest\Api\Entity\Ebsr;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as Entity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;

/**
 * EbsrSubmission Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class EbsrSubmissionEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @dataProvider isDataRefreshProvider
     *
     * @param string $status
     * @param bool $result
     */
    public function testIsDataRefresh($status, $result)
    {
        $entity = m::mock(Entity::class)->makePartial();
        $submissionType = new RefData($status);
        $entity->setEbsrSubmissionType($submissionType);
        $this->assertEquals($result, $entity->isDataRefresh());
    }

    /**
     * Data provider for testIsDataRefresh
     *
     * @return array
     */
    public function isDataRefreshProvider()
    {
        return [
            [Entity::DATA_REFRESH_SUBMISSION_TYPE, true],
            [Entity::NEW_SUBMISSION_TYPE, false]
        ];
    }
}
