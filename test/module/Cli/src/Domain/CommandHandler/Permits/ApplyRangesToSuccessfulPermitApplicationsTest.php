<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

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
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Doctrine\Common\Collections\AbstractLazyCollection;

/**
 * Apply ranges to successful permit applications test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplyRangesToSuccessfulPermitApplicationsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ApplyRangesToSuccessfulPermitApplicationsHandler();
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
                'countryCodes' => []
            ],
            [
                'id' => 2,
                'countryCodes' => []
            ],
            [
                'id' => 3,
                'countryCodes' => []
            ],
            [
                'id' => 4,
                'countryCodes' => []
            ],
            [
                'id' => 5,
                'countryCodes' => ['IT', 'RU']
            ],
            [
                'id' => 6,
                'countryCodes' => ['RU', 'GR', 'AT']
            ],
        ];

        $irhpCandidatePermits = [];
        foreach ($candidatePermits as $candidatePermit) {
            $irhpCandidatePermits[] = $this->createCandidatePermit(
                $candidatePermit['id'],
                $candidatePermit['countryCodes']
            );
        }

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getSuccessfulScoreOrdered')
            ->with($stockId)
            ->andReturn($irhpCandidatePermits);

        // expected mapping of candidate ids to range objects
        $expectedMappings = [
            1 => $irhpPermitRangesByRangeId[1],
            2 => $irhpPermitRangesByRangeId[1],
            3 => $irhpPermitRangesByRangeId[2],
            4 => $irhpPermitRangesByRangeId[6],
            5 => $irhpPermitRangesByRangeId[5],
            6 => $irhpPermitRangesByRangeId[4],
        ];

        foreach ($expectedMappings as $candidatePermitId => $rangeEntity) {
            $this->repoMap['IrhpCandidatePermit']->shouldReceive('updateRange')
                ->with($candidatePermitId, $rangeEntity)
                ->once();
        }

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
