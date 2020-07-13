<?php

/**
 * Does Own CompanySubsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\DoesOwnCompanySubsidiary;
use Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary;
use Mockery as m;

/**
 * Does Own CompanySubsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DoesOwnCompanySubsidiaryTest extends AbstractValidatorsTestCase
{
    /**
     * @var DoesOwnCompanySubsidiary
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new DoesOwnCompanySubsidiary();

        parent::setUp();
    }

    /**
     * @dataProvider provider
     */
    public function testIsValidTrue($isOwner, $expected)
    {
        $entity = m::mock(CompanySubsidiary::class);

        $repo = $this->mockRepo('CompanySubsidiary');
        $repo->shouldReceive('fetchById')->with(111)->andReturn($entity);

        $this->setIsValid('isOwner', [$entity], $isOwner);

        $this->assertEquals($expected, $this->sut->isValid(111));
    }

    public function provider()
    {
        return [
            [true, true],
            [false, false]
        ];
    }
}
