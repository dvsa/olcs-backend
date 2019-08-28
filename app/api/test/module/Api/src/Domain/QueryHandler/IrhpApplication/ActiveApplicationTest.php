<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\ActiveApplication as ActiveApplicationHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\ActiveApplication as QryClass;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitType;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * ActiveApplication Test
 */
class ActiveApplicationTest extends QueryHandlerTestCase
{
    protected $bundle = [
        'licence',
        'irhpPermitType',
    ];

    /**
     * Set up test
     */
    public function setUp()
    {
        $this->sut = new ActiveApplicationHandler();

        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleQueryWithYear()
    {
        $licenceId = 7;
        $irhpPermitTypeId = IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM;
        $year = 2019;

        $irhpApplication1 = $this->createMockApplicationWithYear(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
            false,
            2021
        );

        $irhpApplication2 = $this->createMockApplicationWithYear(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
            true,
            2020
        );

        $irhpApplication3 = $this->createMockApplicationWithYear(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
            false,
            2022
        );

        $irhpApplication4 = $this->createMockApplicationWithYear(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
            true,
            2020
        );

        $irhpApplication5 = $this->createMockApplicationWithYear(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
            true,
            2019
        );

        $irhpApplication6 = $this->createMockApplicationWithYear(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
            false,
            2021
        );

        $serializedIrhpApplication5 = [
            'property1' => 'value1',
            'property2' => 'value2',
            'property3' => 'value3',
        ];

        $irhpApplication5->shouldReceive('serialize')
            ->once()
            ->with($this->bundle)
            ->andReturn($serializedIrhpApplication5);

        $irhpApplications = [
            $irhpApplication1,
            $irhpApplication2,
            $irhpApplication3,
            $irhpApplication4,
            $irhpApplication5,
            $irhpApplication6,
        ];

        $this->repoMap['IrhpApplication']->shouldReceive('fetchByLicence')
            ->with($licenceId)
            ->andReturn($irhpApplications);

        $query = QryClass::create(
            [
                'licence' => $licenceId,
                'irhpPermitType' => $irhpPermitTypeId,
                'year' => $year,
            ]
        );

        $serializedResult = $this->sut->handleQuery($query)->serialize();
        $this->assertEquals($serializedIrhpApplication5, $serializedResult);
    }

    public function testHandleQueryWithYearNull()
    {
        $licenceId = 7;
        $irhpPermitTypeId = IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM;
        $year = 2019;

        $irhpApplication1 = $this->createMockApplicationWithYear(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
            false,
            2021
        );

        $irhpApplication2 = $this->createMockApplicationWithYear(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
            true,
            2020
        );

        $irhpApplication3 = $this->createMockApplicationWithYear(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
            false,
            2022
        );

        $irhpApplication4 = $this->createMockApplicationWithYear(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
            true,
            2020
        );

        $irhpApplication5 = $this->createMockApplicationWithYear(IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, false, 2021);

        $irhpApplications = [
            $irhpApplication1,
            $irhpApplication2,
            $irhpApplication3,
            $irhpApplication4,
            $irhpApplication5,
        ];

        $this->repoMap['IrhpApplication']->shouldReceive('fetchByLicence')
            ->with($licenceId)
            ->andReturn($irhpApplications);

        $query = QryClass::create(
            [
                'licence' => $licenceId,
                'irhpPermitType' => $irhpPermitTypeId,
                'year' => $year,
            ]
        );

        $this->assertNull(
            $this->sut->handleQuery($query)
        );
    }

    public function testHandleQueryWithoutYear()
    {
        $licenceId = 7;
        $irhpPermitTypeId = IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM;
        $year = 2019;

        $irhpApplication1 = $this->createMockApplication(IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, false);
        $irhpApplication2 = $this->createMockApplication(IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, true);
        $irhpApplication3 = $this->createMockApplication(IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, false);
        $irhpApplication4 = $this->createMockApplication(IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, true);
        $irhpApplication5 = $this->createMockApplication(IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, true);

        $serializedIrhpApplication4 = [
            'property1' => 'value1',
            'property2' => 'value2',
            'property3' => 'value3',
        ];

        $irhpApplication4->shouldReceive('serialize')
            ->once()
            ->with($this->bundle)
            ->andReturn($serializedIrhpApplication4);

        $irhpApplications = [
            $irhpApplication1,
            $irhpApplication2,
            $irhpApplication3,
            $irhpApplication4,
            $irhpApplication5,
        ];

        $this->repoMap['IrhpApplication']->shouldReceive('fetchByLicence')
            ->with($licenceId)
            ->andReturn($irhpApplications);

        $query = QryClass::create(
            [
                'licence' => $licenceId,
                'irhpPermitType' => $irhpPermitTypeId,
            ]
        );

        $serializedResult = $this->sut->handleQuery($query)->serialize();
        $this->assertEquals($serializedIrhpApplication4, $serializedResult);
    }

    public function testHandleQueryWithoutYearNull()
    {
        $licenceId = 7;
        $irhpPermitTypeId = IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM;
        $year = 2019;

        $irhpApplication1 = $this->createMockApplication(IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, false);
        $irhpApplication2 = $this->createMockApplication(IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, true);
        $irhpApplication3 = $this->createMockApplication(IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, false);
        $irhpApplication4 = $this->createMockApplication(IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, true);

        $irhpApplications = [
            $irhpApplication1,
            $irhpApplication2,
            $irhpApplication3,
            $irhpApplication4,
        ];

        $this->repoMap['IrhpApplication']->shouldReceive('fetchByLicence')
            ->with($licenceId)
            ->andReturn($irhpApplications);

        $query = QryClass::create(
            [
                'licence' => $licenceId,
                'irhpPermitType' => $irhpPermitTypeId,
            ]
        );

        $this->assertNull(
            $this->sut->handleQuery($query)
        );
    }

    private function createMockApplicationWithYear($irhpPermitType, $isActive, $validityYear)
    {
        $irhpApplication = $this->createMockApplication($irhpPermitType, $isActive);

        $irhpApplication->shouldReceive(
            'getFirstIrhpPermitApplication->getIrhpPermitWindow->getIrhpPermitStock->getValidityYear'
        )->andReturn($validityYear);

        return $irhpApplication;
    }

    private function createMockApplication($irhpPermitTypeId, $isActive)
    {
        $irhpApplication = m::mock(IrhpApplication::class);

        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->andReturn($irhpPermitTypeId);
        $irhpApplication->shouldReceive('isActive')
            ->andReturn($isActive);

        return $irhpApplication;
    }
}
