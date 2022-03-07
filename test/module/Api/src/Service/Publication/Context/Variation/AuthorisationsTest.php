<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\Variation;

use ArrayObject;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\Context\Variation\Authorisations;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class AuthorisationsTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AuthorisationsTest extends MockeryTestCase
{
    /**
     * @var Authorisations
     */
    private $sut;

    private $application;

    private $publicationLink;

    private $context;

    public function setUp(): void
    {
        $this->sut = new Authorisations(
            m::mock(QueryHandlerManager::class)
        );

        $this->application = m::mock(Application::class);

        $this->publicationLink = m::mock(PublicationLink::class);
        $this->publicationLink->shouldReceive('getApplication')
            ->withNoArgs()
            ->andReturn($this->application);

        $this->context = new ArrayObject();

        parent::setUp();
    }

    /**
     * @dataProvider dpSetContextWhenAuthorisationIncreasedOrChangedFromNullToNumeric
     */
    public function testSetContextWhenAuthorisationIncreasedOrChangedFromNullToNumeric(
        $increased,
        $changedFromNullToNumeric
    ) {
        $totAuthLgvVehicles = 7;

        $this->application->shouldReceive('hasLgvAuthorisationIncreased')
            ->withNoArgs()
            ->andReturn($increased);
        $this->application->shouldReceive('hasLgvAuthorisationChangedFromNullToNumeric')
            ->withNoArgs()
            ->andReturn($changedFromNullToNumeric);
        $this->application->shouldReceive('getTotAuthLgvVehicles')
            ->withNoArgs()
            ->andReturn($totAuthLgvVehicles);

        $this->assertSame(
            [
                'authorisation' => 'Light goods vehicles authorised on the licence. ' .
                    'New authorisation will be 7 vehicle(s)'
            ],
            $this->sut->provide($this->publicationLink, $this->context)->getArrayCopy()
        );
    }

    public function dpSetContextWhenAuthorisationIncreasedOrChangedFromNullToNumeric()
    {
        return [
            [true, false],
            [false, true],
            [true, true],
        ];
    }

    public function testDoNothingWhenAuthorisationNotIncreasedAndNotChangedFromNullToNumeric()
    {
        $this->application->shouldReceive('hasLgvAuthorisationIncreased')
            ->withNoArgs()
            ->andReturnFalse();
        $this->application->shouldReceive('hasLgvAuthorisationChangedFromNullToNumeric')
            ->withNoArgs()
            ->andReturnFalse();

        $this->assertCount(
            0,
            $this->sut->provide($this->publicationLink, $this->context)
        );
    }
}
