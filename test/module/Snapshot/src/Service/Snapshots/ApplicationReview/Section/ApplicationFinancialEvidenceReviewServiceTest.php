<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Api\Entity\Application\Application;
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

    public function setUp(): void
    {
        $this->sut = new ApplicationFinancialEvidenceReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider uploadProvider
     */
    public function testGetConfigFromDataWithoutDocs($uploaded, $reviewText)
    {
        $data = [
            'id' => 123,
            'financialEvidenceUploaded' => $uploaded
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
                        'value' => $reviewText
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

    public function uploadProvider()
    {
        return [
            [
                Application::FINANCIAL_EVIDENCE_SEND_IN_POST,
                'application-review-financial-evidence-evidence-post-translated'
            ],
            [
                Application::FINANCIAL_EVIDENCE_UPLOAD_LATER,
                'application-review-financial-evidence-evidence-later-translated'
            ]
        ];
    }

    public function testGetConfigFromDataWithDocs()
    {
        $data = [
            'id' => 123,
            'financialEvidenceUploaded' => Application::FINANCIAL_EVIDENCE_UPLOADED
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

        $document1 = [
            'description' => 'foo.txt'
        ];
        $document2 = [
            'description' => 'bar.txt'
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
