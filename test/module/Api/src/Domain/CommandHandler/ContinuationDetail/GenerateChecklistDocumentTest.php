<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\Command\ContinuationDetail\GenerateChecklistDocument as Command;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail\GenerateChecklistDocument as CommandHandler;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;
use Mockery as m;

/**
 * GenerateChecklistDocumentTest
 */
class GenerateChecklistDocumentTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('ContinuationDetail', Repository\ContinuationDetail::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            ContinuationDetail::STATUS_PRINTING,
            Licence::LICENCE_CATEGORY_PSV,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
        ];

        $this->references = [
            Licence::class => [
                7 => m::mock(Licence::class)->makePartial(),
            ],
            Organisation::class => [
                1 => m::mock(Organisation::class)->makePartial(),
            ],
            TrafficArea::class => [
                'B' => m::mock(TrafficArea::class)->makePartial()->setIsNi(false),
                'N' => m::mock(TrafficArea::class)->makePartial()->setIsNi(true),
            ],
        ];

        parent::initReferences();
    }

    /**
     * @dataProvider dataProviderTemplates
     */
    public function testHandleCommand($expectedTemplate, $goodsOrPsv, $licenceType, $trafficArea)
    {
        $command = Command::create(['id' => 54, 'user' => 65]);

        $continuationDetail = m::mock(ContinuationDetail::class);
        $continuationDetail
            ->shouldReceive('getLicence')->with()->atLeast()->times(1)->andReturn($this->references[Licence::class][7]);

        $continuationDetail->getLicence()
            ->setGoodsOrPsv($this->mapRefData($goodsOrPsv))
            ->setLicenceType($this->mapRefData($licenceType))
            ->setOrganisation($this->mapReference(Organisation::class, 1))
            ->setTrafficArea($this->mapReference(TrafficArea::class, $trafficArea));

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($continuationDetail);

        $docResult = new Result();
        $docResult->addId('document', 101);
        $docResult->addMessage('Document dispatched');

        $dtoData = [
            'template' => $expectedTemplate,
            'query' => [
                'licence' => 7,
                'goodsOrPsv' => $goodsOrPsv,
                'licenceType' => $licenceType,
                'niFlag' => $trafficArea === 'N' ? 'Y' : 'N',
                'organisation' => 1,
                'user' => 65
            ],
            'description' => 'Continuation checklist',
            'licence' => 7,
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS_LICENCE,
            'isExternal'  => false,
            'isScan' => false,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null,
            'issuedDate' => null,
            'dispatch' => true
        ];

        $this->expectedSideEffect(GenerateAndStore::class, $dtoData, $docResult);

        $this->sut->handleCommand($command);
    }

    public function dataProviderTemplates()
    {
        return [
            [
                Document::GV_CONTINUATION_CHECKLIST,
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                'B'
            ],
            [
                Document::GV_CONTINUATION_CHECKLIST,
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                'B'
            ],
            [
                Document::GV_CONTINUATION_CHECKLIST_NI,
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                'N'
            ],
            [
                Document::GV_CONTINUATION_CHECKLIST_NI,
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
                'N'
            ],
            [
                Document::PSV_CONTINUATION_CHECKLIST,
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                'B'
            ],
            [
                Document::PSV_CONTINUATION_CHECKLIST,
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                'N'
            ],
            [
                Document::PSV_CONTINUATION_CHECKLIST_SR,
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
                'B'
            ],
            [
                Document::PSV_CONTINUATION_CHECKLIST_SR,
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
                'N'
            ],
        ];
    }
}
