<?php

/**
 * Variation Operating Centre Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Service;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\Service\VariationOperatingCentreHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Domain\Repository\LicenceOperatingCentre;

/**
 * Variation Operating Centre Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationOperatingCentreHelperTest extends MockeryTestCase
{
    /**
     * @var VariationOperatingCentreHelper
     */
    protected $sut;

    /**
     * @var ApplicationOperatingCentre
     */
    protected $aocRepo;

    /**
     * @var LicenceOperatingCentre
     */
    protected $locRepo;

    public function setUp()
    {
        $this->aocRepo = m::mock();
        $this->locRepo = m::mock();

        $repoSm = m::mock(ServiceLocatorInterface::class);
        $repoSm->shouldReceive('get')
            ->with('ApplicationOperatingCentre')
            ->andReturn($this->aocRepo)
            ->shouldReceive('get')
            ->with('LicenceOperatingCentre')
            ->andReturn($this->locRepo);

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')
            ->with('RepositoryServiceManager')
            ->andReturn($repoSm);

        $this->sut = new VariationOperatingCentreHelper();

        $this->sut->createService($sm);
    }

    public function testGetListDataForApplication()
    {
        $aocData = [
            [
                'id' => 321,
                'action' => 'U',
                'operatingCentre' => [
                    'id' => 123
                ],
                'sort' => 123
            ]
        ];
        $locData = [
            [
                'id' => 654,
                'operatingCentre' => [
                    'id' => 123
                ],
                'sort' => 123
            ],
            [
                'id' => 655,
                'operatingCentre' => [
                    'id' => 999
                ],
                'sort' => 999
            ]
        ];

        $expected = [
            [
                'id' => 'L654',
                'operatingCentre' => [
                    'id' => 123
                ],
                'source' => 'L',
                'action' => 'C',
                'sort' => 123
            ],
            [
                'id' => 'A321',
                'action' => 'U',
                'operatingCentre' => [
                    'id' => 123
                ],
                'source' => 'A',
                'sort' => 123
            ],
            [
                'id' => 'L655',
                'operatingCentre' => [
                    'id' => 999
                ],
                'source' => 'L',
                'action' => 'E',
                'sort' => 999
            ]
        ];

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->setLicence($licence);

        $this->aocRepo->shouldReceive('fetchByApplicationIdForOperatingCentres')
            ->with(111)
            ->andReturn($aocData);

        $this->locRepo->shouldReceive('fetchByLicenceIdForOperatingCentres')
            ->with(222)
            ->andReturn($locData);

        $this->assertEquals($expected, $this->sut->getListDataForApplication($application));
    }
}
