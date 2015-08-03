<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail\Process as CommandHandler;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator as DocGenerator;
use Dvsa\Olcs\Api\Domain\Command\ContinuationDetail\Process as Command;
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

        $this->mockedSmServices['DocumentGenerator'] = m::mock(DocGenerator::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'STATUS',
            ContinuationDetail::STATUS_PRINTING,
            Licence::LICENCE_CATEGORY_PSV,
            Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
        ];

        $this->references = [
            Licence::class => [
                7 => m::mock(Licence::class)->makePartial(),
            ],
            Organisation::class => [
                1 => m::mock(Organisation::class)->makePartial(),
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommandNoOp()
    {
        $id = 69;

        $data = [
            'id' => $id,
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
        $storedFileId = 99;
        $documentId = 101;
        $organisationId = 1;

        $data = [
            'id' => $id,
        ];

        $command = Command::create($data);

        $continuationDetail = new ContinuationDetail();
        $continuationDetail
            ->setId($id)
            ->setStatus($this->mapRefData(ContinuationDetail::STATUS_PRINTING))
            ->setLicence($this->mapReference(Licence::class, $licenceId));
        $continuationDetail
            ->getLicence()
            ->setOrganisation($this->mapReference(Organisation::class, $organisationId));

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn($continuationDetail);

        $continuationDetail->getLicence()
            ->setGoodsOrPsv($this->mapRefData(Licence::LICENCE_CATEGORY_PSV))
            ->setLicenceType($this->mapRefData(Licence::LICENCE_TYPE_SPECIAL_RESTRICTED))
            ->setNiFlag('N');

        $storedFile = m::mock(\Dvsa\Olcs\Api\Service\File\File::class)->makePartial();
        $storedFile
            ->setIdentifier($storedFileId)
            ->setSize(12345);
        $this->mockedSmServices['DocumentGenerator']
            ->shouldReceive('generateAndStore')
            ->with(
                'PSVSRChecklist',
                [
                    'licence' => $licenceId,
                    'goodsOrPsv' => Licence::LICENCE_CATEGORY_PSV,
                    'licenceType' => Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
                    'niFlag' => 'N',
                    'organisation' => $organisationId,
                ]
            )
            ->once()
            ->andReturn($storedFile);

        $docResult = new Result();
        $docResult
            ->addId('document', 101)
            ->addMessage('Document dispatched');
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument::class,
            [
                'identifier' => $storedFileId,
                'size' => 12345,
                'description' => 'Continuation checklist',
                'filename' => 'PSVSRChecklist.rtf',
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
            ],
            $docResult
        );

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

        $result = $this->sut->handleCommand($command);

        $this->assertSame($document, $continuationDetail->getChecklistDocument());

        $this->assertEquals(
            [
                'Document dispatched',
                'ContinuationDetail updated',
            ],
            $result->getMessages()
        );
        $this->assertEquals(
            [
                'continuationDetail' => $id,
                'document' => $documentId,
            ],
            $result->getIds()
        );
    }
}
