<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\Publication;

use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousApplicationPublicationNo;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class PreviousApplicationPublicationNoTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PreviousApplicationPublicationNoTest extends MockeryTestCase
{
    /**
     * @group publicationFilter
     * @dataProvider CheckByApplicationAppStatusProvider
     *
     * Test the previous hearing date filter
     */
    public function testProvideByApplication($appStatus)
    {
        $pubType = 'A&D';
        $trafficArea = 'trafficArea';
        $currentPublicationNo = 889;
        $previousPublicationNo = 888;
        $appId = 66;

        $output = [
            'previousPublication' => $previousPublicationNo,
        ];

        $expectedOutput = new \ArrayObject($output);

        $publication = m::mock(PublicationLink::class);
        $publication->shouldReceive('getPublication->getPubType')->once()->andReturn($pubType);
        $publication->shouldReceive('getPublication->getPublicationNo')->once()->andReturn($currentPublicationNo);
        $publication->shouldReceive('getTrafficArea')->once()->andReturn($trafficArea);
        $publication->shouldReceive('getApplication->getStatus->getId')->andReturn($appStatus);
        $publication->shouldReceive('getApplication->getId')->once()->andReturn($appId);

        $previousPublicationResult = m::mock(PublicationLink::class);
        $previousPublicationResult
            ->shouldReceive('getPublication->getPublicationNo')
            ->once()
            ->andReturn($previousPublicationNo);

        $mockQueryHandler = m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class);
        $mockQueryHandler->shouldReceive('handleQuery')->once()->andReturn($previousPublicationResult);

        $sut = new PreviousApplicationPublicationNo($mockQueryHandler);

        $this->assertEquals($expectedOutput, $sut->provide($publication, new \ArrayObject()));
    }

    /**
     * @group publicationFilter
     * @dataProvider CheckByLicenceAppStatusProvider
     *
     * Test the previous hearing date filter
     */
    public function testProvideByLicence($appStatus)
    {
        $pubType = 'A&D';
        $trafficArea = 'trafficArea';
        $currentPublicationNo = 889;
        $previousPublicationNo = 888;
        $licId = 55;

        $output = [
            'previousPublication' => $previousPublicationNo,
        ];

        $expectedOutput = new \ArrayObject($output);

        $publication = m::mock(PublicationLink::class);
        $publication->shouldReceive('getPublication->getPubType')->once()->andReturn($pubType);
        $publication->shouldReceive('getPublication->getPublicationNo')->once()->andReturn($currentPublicationNo);
        $publication->shouldReceive('getTrafficArea')->once()->andReturn($trafficArea);
        $publication->shouldReceive('getApplication->getStatus->getId')->andReturn($appStatus);
        $publication->shouldReceive('getLicence->getId')->once()->andReturn($licId);

        $previousPublicationResult = m::mock(PublicationLink::class);
        $previousPublicationResult
            ->shouldReceive('getPublication->getPublicationNo')
            ->once()
            ->andReturn($previousPublicationNo);

        $mockQueryHandler = m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class);
        $mockQueryHandler->shouldReceive('handleQuery')->once()->andReturn($previousPublicationResult);

        $sut = new PreviousApplicationPublicationNo($mockQueryHandler);

        $this->assertEquals($expectedOutput, $sut->provide($publication, new \ArrayObject()));
    }

    /**
     * data provider for application status where we get previous publications by application id
     * @return array
     */
    public function checkByLicenceAppStatusProvider()
    {
        return [
            [ApplicationEntity::APPLICATION_STATUS_GRANTED],
            [ApplicationEntity::APPLICATION_STATUS_REFUSED],
            [ApplicationEntity::APPLICATION_STATUS_NOT_TAKEN_UP],
            [ApplicationEntity::APPLICATION_STATUS_CURTAILED]
        ];
    }

    /**
     * data provider for application status where we get previous publications by application id
     * @return array
     */
    public function checkByApplicationAppStatusProvider()
    {
        return [
            [ApplicationEntity::APPLICATION_STATUS_WITHDRAWN],
            [ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION]
        ];
    }
}
