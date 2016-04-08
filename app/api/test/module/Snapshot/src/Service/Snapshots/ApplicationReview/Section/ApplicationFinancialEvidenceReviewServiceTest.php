<?php

/**
 * Application Financial Evidence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Api\Entity\Doc\Document;
use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\ApplicationFinancialEvidenceReviewService;

/**
 * Application Financial Evidence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationFinancialEvidenceReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new ApplicationFinancialEvidenceReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromDataWithoutDocs()
    {
        $data = [
            'id' => 123,
            'financialEvidenceUploaded' => 'N'
        ];

        $expected = [
            'multiItems' => [
                [
                    [
                        'label' => 'application-review-financial-evidence-no-of-vehicles',
                        'value' => 6
                    ],
                    [
                        'label' => 'application-review-financial-evidence-required-finance',
                        'value' => '£123,456'
                    ],
                    [
                        'label' => 'application-review-financial-evidence-evidence',
                        'noEscape' => true,
                        'value' => 'application-review-financial-evidence-evidence-post-translated'
                    ]
                ]
            ]
        ];

        $qhManager = m::mock();
        $qhManager->shouldReceive('handleQuery->serialize')
            ->andReturn(
                [
                    'financialEvidence' => [
                        'requiredFinance' => 123456,
                        'applicationVehicles' => 1,
                        'otherLicenceVehicles' => 2,
                        'otherApplicationVehicles' => 3
                    ],
                ]
            );

        $this->sm->setService('QueryHandlerManager', $qhManager);

        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function testGetConfigFromDataWithDocs()
    {
        $data = [
            'id' => 123,
            'financialEvidenceUploaded' => 'Y'
        ];

        $expected = [
            'multiItems' => [
                [
                    [
                        'label' => 'application-review-financial-evidence-no-of-vehicles',
                        'value' => 6
                    ],
                    [
                        'label' => 'application-review-financial-evidence-required-finance',
                        'value' => '£123,456'
                    ],
                    [
                        'label' => 'application-review-financial-evidence-evidence',
                        'noEscape' => true,
                        'value' => 'foo.txt<br>bar.txt'
                    ]
                ]
            ]
        ];

        $document1 = m::mock(Document::class)->makePartial();
        $document1->setDescription('foo.txt');
        $document2 = m::mock(Document::class)->makePartial();
        $document2->setDescription('bar.txt');

        $qhManager = m::mock();
        $qhManager->shouldReceive('handleQuery->serialize')
            ->andReturn(
                [
                    'financialEvidence' => [
                        'requiredFinance' => 123456,
                        'applicationVehicles' => 1,
                        'otherLicenceVehicles' => 2,
                        'otherApplicationVehicles' => 3
                    ],
                    'documents' => [
                        $document1,
                        $document2
                    ]
                ]
            );

        $this->sm->setService('QueryHandlerManager', $qhManager);

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
