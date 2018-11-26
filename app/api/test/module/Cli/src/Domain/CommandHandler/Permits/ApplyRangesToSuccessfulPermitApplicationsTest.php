<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as IrhpPermitRangeRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\Olcs\Cli\Domain\Command\Permits\ApplyRangesToSuccessfulPermitApplications
    as ApplyRangesToSuccessfulPermitApplicationsCommand;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\ApplyRangesToSuccessfulPermitApplications
    as ApplyRangesToSuccessfulPermitApplicationsHandler;
use Dvsa\Olcs\Transfer\Query\Permits\EcmtConstrainedCountriesList;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Apply ranges to successful permit applications test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplyRangesToSuccessfulPermitApplicationsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = m::mock(ApplyRangesToSuccessfulPermitApplicationsHandler::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);
        $this->mockRepo('IrhpPermitRange', IrhpPermitRangeRepo::class);
        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);

        parent::setUp();
    }

    /**
     * tests handleCommand
     */
    public function testHandleCommand()
    {
        $stockId = 8;

        $this->sut->shouldReceive('handleQuery')
            ->with(m::type(EcmtConstrainedCountriesList::class))
            ->andReturnUsing(function ($query) {
                $this->assertEquals(1, $query->hasEcmtConstraints());

                return [
                    'result' => [
                        ['id' => 'AT'],
                        ['id' => 'GR'],
                        ['id' => 'HU'],
                        ['id' => 'IT'],
                        ['id' => 'RU']
                    ]
                ];
            });

        $ranges = [
            [
                'id' => 1,
                'size' => 4,
                'fromNo' => 100,
                'permitCount' => 2,
                'countryCodes' => []
            ],
            [
                'id' => 2,
                'size' => 5,
                'fromNo' => 200,
                'permitCount' => 4,
                'countryCodes' => []
            ],
            [
                'id' => 3,
                'size' => 2,
                'fromNo' => 300,
                'permitCount' => 0,
                'countryCodes' => ['IT']
            ],
            [
                'id' => 4,
                'size' => 2,
                'fromNo' => 400,
                'permitCount' => 1,
                'countryCodes' => ['IT', 'RU', 'GR', 'AT']
            ],
            [
                'id' => 5,
                'size' => 2,
                'fromNo' => 500,
                'permitCount' => 1,
                'countryCodes' => ['IT', 'RU', 'HU']
            ],
            [
                'id' => 6,
                'size' => 4,
                'fromNo' => 600,
                'permitCount' => 3,
                'countryCodes' => []
            ],
        ];

        $irhpPermitRanges = [];
        $irhpPermitRangesByRangeId = [];

        foreach ($ranges as $range) {
            $irhpPermitRange = $this->createRange(
                $range['id'],
                $range['size'],
                $range['fromNo'],
                $range['countryCodes']
            );

            $irhpPermitRanges[] = $irhpPermitRange;
            $irhpPermitRangesByRangeId[$range['id']] = $irhpPermitRange;

            $this->repoMap['IrhpPermit']->shouldReceive('getPermitCountByRange')
                ->with($range['id'])
                ->andReturn($range['permitCount']);
        }

        $this->repoMap['IrhpPermitRange']->shouldReceive('getByStockId')
            ->with($stockId)
            ->andReturn($irhpPermitRanges);

        $candidatePermits = [
            [
                'id' => 1,
                'countryCodes' => [],
                'expectedRange' => $irhpPermitRangesByRangeId[1]
            ],
            [
                'id' => 2,
                'countryCodes' => [],
                'expectedRange' => $irhpPermitRangesByRangeId[1]
            ],
            [
                'id' => 3,
                'countryCodes' => [],
                'expectedRange' => $irhpPermitRangesByRangeId[2]
            ],
            [
                'id' => 4,
                'countryCodes' => [],
                'expectedRange' => $irhpPermitRangesByRangeId[6]
            ],
            [
                'id' => 5,
                'countryCodes' => ['IT', 'RU'],
                'expectedRange' => $irhpPermitRangesByRangeId[5]
            ],
            [
                'id' => 6,
                'countryCodes' => ['RU', 'GR', 'AT'],
                'expectedRange' => $irhpPermitRangesByRangeId[4]
            ],
        ];

        $irhpCandidatePermits = [];
        foreach ($candidatePermits as $candidatePermit) {
            $irhpCandidatePermit = $this->createCandidatePermit(
                $candidatePermit['id'],
                $candidatePermit['countryCodes']
            );

            $irhpCandidatePermit->shouldReceive('applyRange')
                ->with($candidatePermit['expectedRange'])
                ->once()
                ->ordered()
                ->globally();

            $this->repoMap['IrhpCandidatePermit']->shouldReceive('saveOnFlush')
                ->with($irhpCandidatePermit)
                ->once()
                ->ordered()
                ->globally();

            $irhpCandidatePermits[] = $irhpCandidatePermit;
        }

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getSuccessfulScoreOrderedInScope')
            ->with($stockId)
            ->andReturn($irhpCandidatePermits);

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('flushAll')
            ->once()
            ->ordered()
            ->globally();

        $this->sut->handleCommand(
            ApplyRangesToSuccessfulPermitApplicationsCommand::create(['stockId' => $stockId])
        );
    }

    private function createRange($id, $size, $fromNo, $countryCodes)
    {
        $persistentCollection = $this->createCountriesPersistentCollection($countryCodes);

        $range = m::mock(IrhpPermitRange::class);
        $range->shouldReceive('getId')
            ->andReturn($id);
        $range->shouldReceive('getSize')
            ->andReturn($size);
        $range->shouldReceive('getFromNo')
            ->andReturn($fromNo);
        $range->shouldReceive('getCountrys')
            ->andReturn($persistentCollection);

        return $range;
    }

    private function createCandidatePermit($id, $countryCodes)
    {
        $persistentCollection = $this->createCountriesPersistentCollection($countryCodes);

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getCountrys')
            ->andReturn($persistentCollection);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getEcmtPermitApplication')
            ->andReturn($ecmtPermitApplication);

        $candidatePermit = m::mock(IrhpCandidatePermit::class);
        $candidatePermit->shouldReceive('getId')
            ->andReturn($id);
        $candidatePermit->shouldReceive('getIrhpPermitApplication')
            ->andReturn($irhpPermitApplication);
        $candidatePermit->shouldReceive('getRandomizedScore')
            ->andReturn(0);

        return $candidatePermit;
    }

    private function createCountriesPersistentCollection($countryCodes)
    {
        $countries = [];
        foreach ($countryCodes as $countryCode) {
            $country = m::mock(Country::class);
            $country->shouldReceive('getId')
                ->andReturn($countryCode);
            $countries[] = $country;
        }

        $persistentCollection = m::mock(AbstractLazyCollection::class);
        $persistentCollection->shouldReceive('getValues')
            ->andReturn($countries);

        return $persistentCollection;
    }
}
