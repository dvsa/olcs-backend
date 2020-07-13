<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section\SafetyReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use OlcsTest\Bootstrap;
use Zend\I18n\View\Helper\Translate;

/**
 * Safety review service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SafetyReviewServiceTest extends MockeryTestCase
{
    /** @var SafetyReviewService review service */
    protected $sut;

    protected $mockTranslator;

    public function setUp(): void
    {
        $this->mockTranslator = m::mock(Translate::class)
            ->shouldReceive('translate')
            ->andReturnUsing(
                function ($arg) {
                    return $arg . '_translated';
                }
            )
            ->getMock();

        $this->sut = new SafetyReviewService();
        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
        $this->sm->setService('translator', $this->mockTranslator);
    }

    public function testGetConfigFromData()
    {
        $continuationDetail = new ContinuationDetail();

        $workshops = new ArrayCollection();

        $workshop1 = m::mock()
            ->shouldReceive('getContactDetails')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getAddress')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getAddressLine1')
                            ->andReturn('Cake')
                            ->once()
                            ->shouldReceive('getTown')
                            ->andReturn('Baz')
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('getFao')
                    ->andReturn('Name 2')
                    ->once()
                    ->getMock()
            )
            ->shouldReceive('getIsExternal')
            ->andReturn('N')
            ->once()
            ->getMock();

        $workshop2 = m::mock()
            ->shouldReceive('getContactDetails')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getAddress')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getAddressLine1')
                            ->andReturn('Foo')
                            ->once()
                            ->shouldReceive('getTown')
                            ->andReturn('Bar')
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('getFao')
                    ->andReturn('Name 1')
                    ->once()
                    ->getMock()
            )
            ->shouldReceive('getIsExternal')
            ->andReturn('Y')
            ->once()
            ->getMock();

        $workshops->add($workshop1);
        $workshops->add($workshop2);

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getWorkshops')
            ->andReturn($workshops)
            ->once()
            ->getMock();

        $continuationDetail->setLicence($mockLicence);

        $expected = [
            [
                ['value' => 'continuations.safety-section.table.inspector', 'header' => true],
                ['value' => 'continuations.safety-section.table.address', 'header' => true],
            ],
            [
                ['value' => 'Name 1 (continuations.safety-section.table.external-contractor_translated)'],
                ['value' => 'Foo, Bar'],
            ],
            [
                ['value' => 'Name 2 (continuations.safety-section.table.owner-or-employee_translated)'],
                ['value' => 'Cake, Baz'],
            ]
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($continuationDetail));
    }

    public function testGetSummaryFromData()
    {
        $continuationDetail = new ContinuationDetail();

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getSafetyInsVehicles')
            ->andReturn(2)
            ->times(3)
            ->shouldReceive('isGoods')
            ->andReturn(true)
            ->once()
            ->shouldReceive('getSafetyInsTrailers')
            ->andReturn(3)
            ->times(3)
            ->shouldReceive('getSafetyInsVaries')
            ->andReturn('Y')
            ->times(2)
            ->shouldReceive('getTachographIns')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(Licence::TACH_EXT)
                ->once()
                ->getMock()
            )
            ->twice()
            ->shouldReceive('getTachographInsName')
            ->andReturn('foo')
            ->twice()
            ->getMock();

        $continuationDetail->setLicence($mockLicence);

        $expected = [
            [
                [
                    'value' => 'continuations.safety-section.table.max-time-vehicles_translated',
                    'header' => true
                ],
                [
                    'value' => '2 ' . 'continuations.safety-section.table.weeks_translated',
                ]
            ],
            [
                [
                    'value' => 'continuations.safety-section.table.max-time-trailers_translated',
                    'header' => true
                ],
                [
                    'value' => '3 ' . 'continuations.safety-section.table.weeks_translated',
                ]
            ],
            [
                [
                    'value' => 'continuations.safety-section.table.varies_translated',
                    'header' => true
                ],
                [
                    'value' => 'Yes_translated',
                ]
            ],
            [
                [
                    'value' => 'continuations.safety-section.table.tachographs_translated',
                    'header' => true
                ],
                [
                    'value' => 'continuations.safety-section.table.' . Licence::TACH_EXT . '_translated',
                ]
            ],
            [
                [
                    'value' => 'continuations.safety-section.table.tachographInsName_translated',
                    'header' => true
                ],
                [
                    'value' => 'foo',
                ]
            ],
        ];

        $this->assertEquals($expected, $this->sut->getSummaryFromData($continuationDetail));
    }

    public function testGetSummaryHeader()
    {
        $this->assertEquals(
            'continuations.safety-details.label',
            $this->sut->getSummaryHeader(new ContinuationDetail())
        );
    }
}
