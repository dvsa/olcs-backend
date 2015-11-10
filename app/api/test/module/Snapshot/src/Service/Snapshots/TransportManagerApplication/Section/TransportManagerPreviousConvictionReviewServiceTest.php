<?php

/**
 * Transport Manager Previous Conviction Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Api\Entity;

/**
 * Transport Manager Previous Conviction Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerPreviousConvictionReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new TransportManagerApplication\Section\TransportManagerPreviousConvictionReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider provider
     */
    public function testGetConfig($tma, $expected)
    {
        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $this->assertEquals($expected, $this->sut->getConfig($tma));
    }

    public function provider()
    {
        /** @var Entity\Tm\TransportManager $tm1 */
        $tm1 = m::mock(Entity\Tm\TransportManager::class)->makePartial();
        $tm1->setPreviousConvictions(new ArrayCollection());

        /** @var Entity\Tm\TransportManagerApplication $tma1 */
        $tma1 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();
        $tma1->setTransportManager($tm1);

        /** @var Entity\Application\PreviousConviction $conviction1 */
        $conviction1 = m::mock(Entity\Application\PreviousConviction::class)->makePartial();
        $conviction1->setCategoryText('Some conviction');
        $conviction1->setConvictionDate(new DateTime('2014-10-01'));
        $conviction1->setNotes('Conviction notes');
        $conviction1->setCourtFpn('Some court name');
        $conviction1->setPenalty('Some penalty');

        /** @var Entity\Application\PreviousConviction $conviction2 */
        $conviction2 = m::mock(Entity\Application\PreviousConviction::class)->makePartial();
        $conviction2->setCategoryText('Some other conviction');
        $conviction2->setConvictionDate(new DateTime('2014-10-02'));
        $conviction2->setNotes('More conviction notes');
        $conviction2->setCourtFpn('Some other court name');
        $conviction2->setPenalty('Some other penalty');

        $convictions = new ArrayCollection();
        $convictions->add($conviction1);
        $convictions->add($conviction2);

        /** @var Entity\Tm\TransportManager $tm2 */
        $tm2 = m::mock(Entity\Tm\TransportManager::class)->makePartial();
        $tm2->setPreviousConvictions($convictions);

        /** @var Entity\Tm\TransportManagerApplication $tma2 */
        $tma2 = m::mock(Entity\Tm\TransportManagerApplication::class)->makePartial();
        $tma2->setTransportManager($tm2);

        return [
            [
                $tma1,
                [
                    'subSections' => [
                        [
                            'mainItems' => [
                                [
                                    'freetext' => 'tm-review-previous-conviction-none-translated'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                $tma2,
                [
                    'subSections' => [
                        [
                            'mainItems' => [
                                [
                                    'header' => 'Some conviction',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-previous-conviction-date',
                                                'value' => '01 Oct 2014'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-offence',
                                                'value' => 'Some conviction'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-offence-details',
                                                'value' => 'Conviction notes'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-court',
                                                'value' => 'Some court name'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-penalty',
                                                'value' => 'Some penalty'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'Some other conviction',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-previous-conviction-date',
                                                'value' => '02 Oct 2014'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-offence',
                                                'value' => 'Some other conviction'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-offence-details',
                                                'value' => 'More conviction notes'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-court',
                                                'value' => 'Some other court name'
                                            ],
                                            [
                                                'label' => 'tm-review-previous-conviction-penalty',
                                                'value' => 'Some other penalty'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
