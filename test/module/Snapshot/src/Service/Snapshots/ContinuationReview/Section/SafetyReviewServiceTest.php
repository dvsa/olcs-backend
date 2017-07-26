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

    public function setUp()
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

        $expected =[
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
}
