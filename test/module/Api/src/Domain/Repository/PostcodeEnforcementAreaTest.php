<?php

/**
 * Postcode Enforcement Area Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Entity\EnforcementArea\PostcodeEnforcementArea;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\PostcodeEnforcementArea as PostcodeEnforcementAreaRepo;

/**
 * Postcode Enforcement Area Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PostcodeEnforcementAreaTest extends RepositoryTestCase
{
    public function setUp(): void
    {
        $this->setUpSut(PostcodeEnforcementAreaRepo::class);
    }

    public function testFetchByPostcodeId()
    {
        $postcodeId = 'AB1';

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('findOneBy')
            ->with(['postcodeId' => 'AB1'])
            ->andReturn('RESULT');

        $this->em->shouldReceive('getRepository')
            ->with(PostcodeEnforcementArea::class)
            ->andReturn($repo);

        $this->assertEquals('RESULT', $this->sut->fetchByPostcodeId($postcodeId));
    }
}
