<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section\FinanceReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use OlcsTest\Bootstrap;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * FinanceReviewServiceTest
 */
class FinanceReviewServiceTest extends MockeryTestCase
{
    /** @var DeclarationReviewService */
    protected $sut;

    /** @var ContinuationDetail */
    private $continuationDetail;

    /** @var  ServiceLocatorInterface */
    private $serviceManager;

    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();

        $mockLicence = m::mock();
        $mockLicence->shouldReceive('getOrganisation->getId')->once()->with()->andReturn(123);

        $this->continuationDetail = new ContinuationDetail();
        $this->continuationDetail->setId(99);
        $this->continuationDetail->setLicence($mockLicence);

        $mockFinancialService = m::mock();
        $mockFinancialService->shouldReceive('getFinanceCalculationForOrganisation')->once()->with(123)->andReturn(100);
        $this->serviceManager->setService('FinancialStandingHelperService', $mockFinancialService);

        $mockTranslator = m::mock();
        $mockTranslator->shouldReceive('translate')->andReturnUsing(
            function ($message) {
                return $message . '_translated';
            }
        );
        $this->serviceManager->setService('translator', $mockTranslator);

        $this->sut = new FinanceReviewService();
        $this->sut->setServiceLocator($this->serviceManager);
    }

    public function testGetConfigFrom()
    {
        $result = $this->sut->getConfigFromData($this->continuationDetail);
        $this->assertArrayHasKey('mainItems', $result);
        $this->assertCount(1, $result['mainItems']);
        $this->assertArrayHasKey('items', $result['mainItems'][0]);
        $this->assertCount(7, $result['mainItems'][0]['items']);

        $this->assertSame(
            [
                'label' => 'continuations.finance.financial-amount-required',
                'value' => '&pound;100.00',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][0]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.average-balance-amount',
                'value' => 'None_translated',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][1]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.overdraft-facility',
                'value' => 'None_translated',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][2]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.factoring-amount',
                'value' => 'None_translated',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][3]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.other-available-finances',
                'value' => 'None_translated',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][4]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.finances-are-sufficient',
                'value' => 'No_translated',
            ],
            $result['mainItems'][0]['items'][5]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.financial-evidence',
                'value' => 'None_translated',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][6]
        );
    }

    public function testGetConfigFromFinanceRequiredLessThanAvailable()
    {
        $this->continuationDetail->setAverageBalanceAmount(45);
        $this->continuationDetail->setOverdraftAmount(46);
        $this->continuationDetail->setFactoringAmount(47);

        $result = $this->sut->getConfigFromData($this->continuationDetail);
        $this->assertCount(5, $result['mainItems'][0]['items']);

        $this->assertSame(
            [
                'label' => 'continuations.finance.financial-amount-required',
                'value' => '&pound;100.00',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][0]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.average-balance-amount',
                'value' => '&pound;45.00',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][1]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.overdraft-facility',
                'value' => '&pound;46.00',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][2]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.factoring-amount',
                'value' => '&pound;47.00',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][3]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.finances-are-sufficient',
                'value' => 'Yes_translated',
            ],
            $result['mainItems'][0]['items'][4]
        );
    }

    public function testGetConfigFromFinanceRequiredGreaterThanAvailable()
    {
        $this->continuationDetail->setAverageBalanceAmount(4);
        $this->continuationDetail->setOverdraftAmount(5);
        $this->continuationDetail->setFactoringAmount(6);
        $this->continuationDetail->setOtherFinancesAmount(7);
        $this->continuationDetail->setOtherFinancesDetails('FOO BAR');

        $result = $this->sut->getConfigFromData($this->continuationDetail);
        $this->assertCount(8, $result['mainItems'][0]['items']);

        $this->assertSame(
            [
                'label' => 'continuations.finance.financial-amount-required',
                'value' => '&pound;100.00',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][0]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.average-balance-amount',
                'value' => '&pound;4.00',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][1]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.overdraft-facility',
                'value' => '&pound;5.00',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][2]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.factoring-amount',
                'value' => '&pound;6.00',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][3]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.other-available-finances',
                'value' => '&pound;7.00',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][4]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.where-do-these-finances-come-from',
                'value' => 'FOO BAR',
            ],
            $result['mainItems'][0]['items'][5]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.finances-are-sufficient',
                'value' => 'No_translated',
            ],
            $result['mainItems'][0]['items'][6]
        );
        $this->assertSame(
            [
                'label' => 'continuations.finance.financial-evidence',
                'value' => 'None_translated',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][7]
        );
    }

    public function testGetConfigFromDataSendInPost()
    {
        $this->continuationDetail->setFinancialEvidenceUploaded(false);
        $result = $this->sut->getConfigFromData($this->continuationDetail);
        $this->assertCount(7, $result['mainItems'][0]['items']);

        $this->assertSame(
            [
                'label' => 'continuations.finance.financial-evidence',
                'value' => 'continuations.finance.send-in-post_translated',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][6]
        );
    }

    public function testGetConfigFromDataFilesUploaded()
    {
        $document1 = new Document(1);
        $document1->setDescription('document1');
        $document2 = new Document(2);
        $document2->setDescription('document2');

        $mockDocumentRepo = m::mock();
        $mockDocumentRepo->shouldReceive('fetchListForContinuationDetail')->with(99)->once()->andReturn(
            [$document1, $document2]
        );

        $mockRepository = m::mock();
        $mockRepository->shouldReceive('get')->with('Document')->once()->andReturn($mockDocumentRepo);

        $this->serviceManager->setService('RepositoryServiceManager', $mockRepository);

        $this->continuationDetail->setFinancialEvidenceUploaded(true);
        $result = $this->sut->getConfigFromData($this->continuationDetail);
        $this->assertCount(7, $result['mainItems'][0]['items']);

        $this->assertSame(
            [
                'label' => 'continuations.finance.financial-evidence',
                'value' => 'document1<br>document2',
                'noEscape' => true,
            ],
            $result['mainItems'][0]['items'][6]
        );
    }
}
