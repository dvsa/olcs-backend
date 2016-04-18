<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\Command\ContinuationDetail\Process as Command;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail\Process as CommandHandler;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * ProcessTest
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ProcessTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('ContinuationDetail', \Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail::class);
        $this->mockRepo('Document', \Dvsa\Olcs\Api\Domain\Repository\Document::class);
        $this->mockRepo('Fee', \Dvsa\Olcs\Api\Domain\Repository\Fee::class);
        $this->mockRepo('FeeType', \Dvsa\Olcs\Api\Domain\Repository\FeeType::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'STATUS',
            ContinuationDetail::STATUS_PRINTING,
            Licence::LICENCE_CATEGORY_PSV,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            FeeType::FEE_TYPE_CONT,
        ];

        $this->references = [
            Licence::class => [
                7 => m::mock(Licence::class)->makePartial(),
            ],
            Organisation::class => [
                1 => m::mock(Organisation::class)->makePartial(),
            ],
            FeeType::class => [
                999 => m::mock(FeeType::class)->makePartial()
                    ->shouldReceive('getFixedValue')
                    ->andReturn('123.45')
                    ->shouldReceive('getDescription')
                    ->andReturn('Test continuation fee')
                    ->getMock(),
            ],
            TrafficArea::class => [
                'B' => m::mock(TrafficArea::class)->makePartial(),
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommandNoOp()
    {
        $id = 69;

        $data = [
            'id' => $id,
            'user' => 1
        ];

        $command = Command::create($data);

        $continuationDetail = new ContinuationDetail();
        $continuationDetail
            ->setId($id)
            ->setStatus($this->mapRefData('STATUS'));

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn($continuationDetail);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(['Continuation detail no longer pending'], $result->getMessages());
        $this->assertEquals(['continuationDetail' => $id], $result->getIds());
    }

    public function testHandleCommand()
    {
        $id = 69;
        $licenceId = 7;
        $documentId = 101;
        $organisationId = 1;
        $licNo = 'OB1234567';
        $feeId = 102;
        $feeTypeId = 999;
        $userId = 1;

        $data = [
            'id' => $id,
            'user' => $userId
        ];

        $command = Command::create($data);

        $continuationDetail = new ContinuationDetail();
        $continuationDetail
            ->setId($id)
            ->setStatus($this->mapRefData(ContinuationDetail::STATUS_PRINTING))
            ->setLicence($this->mapReference(Licence::class, $licenceId));

        $continuationDetail->getLicence()
            ->setGoodsOrPsv($this->mapRefData(Licence::LICENCE_CATEGORY_PSV))
            ->setLicenceType($this->mapRefData(Licence::LICENCE_TYPE_SPECIAL_RESTRICTED))
            ->setOrganisation($this->mapReference(Organisation::class, $organisationId))
            ->setTrafficArea($this->mapReference(TrafficArea::class, 'B'))
            ->setLicNo($licNo);

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn($continuationDetail);

        $docResult = new Result();
        $docResult->addId('document', 101);
        $docResult->addMessage('Document dispatched');

        $dtoData = [
            'template' => 1303,
            'query' => [
                'licence' => $licenceId,
                'goodsOrPsv' => Licence::LICENCE_CATEGORY_PSV,
                'licenceType' => Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
                'niFlag' => 'N',
                'organisation' => $organisationId,
                'user' => $userId
            ],
            'description' => 'Continuation checklist',
            'licence' => $licenceId,
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS_LICENCE,
            'isReadOnly'  => 'Y',
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

        $document = m::mock();
        $this->repoMap['Document']
            ->shouldReceive('fetchById')
            ->with($documentId)
            ->once()
            ->andReturn($document);

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('save')
            ->with($continuationDetail)
            ->once();

        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingContinuationFeesByLicenceId')
            ->once()
            ->with($licenceId, m::type(\DateTime::class))
            ->andReturn([]);

        $now = new DateTime();
        $this->repoMap['FeeType']
            ->shouldReceive('fetchLatest')
            ->once()
            ->with(
                $this->mapRefData(FeeType::FEE_TYPE_CONT),
                $this->mapRefData(Licence::LICENCE_CATEGORY_PSV),
                $this->mapRefData(Licence::LICENCE_TYPE_SPECIAL_RESTRICTED),
                m::on(
                    // compare date objects
                    function ($arg) use ($now) {
                        return $arg == $now;
                    }
                ),
                $this->mapReference(TrafficArea::class, 'B')
            )
            ->andReturn($this->mapReference(FeeType::class, $feeTypeId));

        $feeResult = new Result();
        $feeResult
            ->addId('fee', $feeId)
            ->addMessage('Fee created');
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee::class,
            [
                'feeType' => $feeTypeId,
                'feeStatus' => Fee::STATUS_OUTSTANDING,
                'amount' => '123.45',
                'invoicedDate' => $now->format('Y-m-d'),
                'licence' => $licenceId,
                'description' => 'Test continuation fee for licence OB1234567',
                'application' => null,
                'busReg' => null,
                'task' => null,
                'irfoGvPermit' => null,
            ],
            $feeResult
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame($document, $continuationDetail->getChecklistDocument());

        $this->assertEquals(
            [
                'Document dispatched',
                'ContinuationDetail updated',
                'Fee created'
            ],
            $result->getMessages()
        );
        $this->assertEquals(
            [
                'continuationDetail' => $id,
                'document' => $documentId,
                'fee' => $feeId,
            ],
            $result->getIds()
        );
    }

    public function testHandleCommandNiGoods()
    {
        $id = 69;
        $licenceId = 7;
        $documentId = 101;
        $organisationId = 1;
        $licNo = 'ON1234567';
        $feeId = 102;
        $feeTypeId = 999;
        $userId = 1;

        $data = [
            'id' => $id,
            'user' => $userId
        ];

        $command = Command::create($data);

        $licence = $this->mapReference(Licence::class, $licenceId);
        $licence->shouldReceive('getNiFlag')->andReturn('Y');

        $continuationDetail = new ContinuationDetail();
        $continuationDetail
            ->setId($id)
            ->setStatus($this->mapRefData(ContinuationDetail::STATUS_PRINTING))
            ->setLicence($licence);

        $continuationDetail->getLicence()
            ->setGoodsOrPsv($this->mapRefData(Licence::LICENCE_CATEGORY_GOODS_VEHICLE))
            ->setLicenceType($this->mapRefData(Licence::LICENCE_TYPE_STANDARD_NATIONAL))
            ->setOrganisation($this->mapReference(Organisation::class, $organisationId))
            ->setTrafficArea($this->mapReference(TrafficArea::class, 'N'))
            ->setLicNo($licNo);

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn($continuationDetail);

        $docResult = new Result();
        $docResult->addId('document', 101);
        $docResult->addMessage('Document dispatched');

        $dtoData = [
            'template' => 1501,
            'query' => [
                'licence' => $licenceId,
                'goodsOrPsv' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                'licenceType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                'niFlag' => 'Y',
                'organisation' => $organisationId,
                'user' => $userId
            ],
            'description' => 'Continuation checklist',
            'licence' => $licenceId,
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS_LICENCE,
            'isReadOnly'  => 'Y',
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

        $document = m::mock();
        $this->repoMap['Document']
            ->shouldReceive('fetchById')
            ->with($documentId)
            ->once()
            ->andReturn($document);

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('save')
            ->with($continuationDetail)
            ->once();

        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingContinuationFeesByLicenceId')
            ->once()
            ->with($licenceId, m::type(\DateTime::class))
            ->andReturn([]);

        $now = new DateTime();
        $this->repoMap['FeeType']
            ->shouldReceive('fetchLatest')
            ->once()
            ->with(
                $this->mapRefData(FeeType::FEE_TYPE_CONT),
                $this->mapRefData(Licence::LICENCE_CATEGORY_GOODS_VEHICLE),
                $this->mapRefData(Licence::LICENCE_TYPE_STANDARD_NATIONAL),
                m::on(
                    // compare date objects
                    function ($arg) use ($now) {
                        return $arg == $now;
                    }
                ),
                $this->mapReference(TrafficArea::class, 'N')
            )
            ->andReturn($this->mapReference(FeeType::class, $feeTypeId));

        $feeResult = new Result();
        $feeResult
            ->addId('fee', $feeId)
            ->addMessage('Fee created');
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee::class,
            [
                'feeType' => $feeTypeId,
                'feeStatus' => Fee::STATUS_OUTSTANDING,
                'amount' => '123.45',
                'invoicedDate' => $now->format('Y-m-d'),
                'licence' => $licenceId,
                'description' => 'Test continuation fee for licence ON1234567',
                'application' => null,
                'busReg' => null,
                'task' => null,
                'irfoGvPermit' => null,
            ],
            $feeResult
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame($document, $continuationDetail->getChecklistDocument());

        $this->assertEquals(
            [
                'Document dispatched',
                'ContinuationDetail updated',
                'Fee created'
            ],
            $result->getMessages()
        );
        $this->assertEquals(
            [
                'continuationDetail' => $id,
                'document' => $documentId,
                'fee' => $feeId,
            ],
            $result->getIds()
        );
    }
}
