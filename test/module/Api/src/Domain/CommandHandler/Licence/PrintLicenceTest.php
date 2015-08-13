<?php

/**
 * Create Psv Discs Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\PrintLicence;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Transfer\Command\Licence\PrintLicence as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

/**
 * Create Psv Discs Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PrintLicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PrintLicence();
        $this->mockRepo('Licence', Licence::class);

        $this->mockedSmServices['DocumentGenerator'] = m::mock(DocumentGenerator::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommandGoods()
    {
        $command = Cmd::create(['id' => 111]);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->shouldReceive('isGoods')->andReturn(true);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $storedFile = m::mock(File::class);
        $storedFile->shouldReceive('getIdentifier')
            ->andReturn('ABC123');

        $this->mockedSmServices['DocumentGenerator']->shouldReceive('generateAndStore')
            ->once()
            ->with('GV_LICENCE_V1', ['licence' => 111])
            ->andReturn($storedFile);

        $data = [
            'identifier' => 'ABC123',
            'description' => 'GV Licence',
            'filename' => 'GV_Licence.rtf',
            'licence' => 111,
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isReadOnly' => true,
            'isExternal' => false,
            'size' => null,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null,
            'isScan' => 0,
            'issuedDate' => null
        ];
        $result1 = new Result();
        $result1->addMessage('Document dispatched');
        $this->expectedSideEffect(DispatchDocument::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Document generated',
                'Document dispatched'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandPsvSr()
    {
        $command = Cmd::create(['id' => 111]);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->shouldReceive('isGoods')->andReturn(false);
        $licence->shouldReceive('isSpecialRestricted')->andReturn(true);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $storedFile = m::mock(File::class);
        $storedFile->shouldReceive('getIdentifier')
            ->andReturn('ABC123');

        $this->mockedSmServices['DocumentGenerator']->shouldReceive('generateAndStore')
            ->once()
            ->with('PSVSRLicence', ['licence' => 111])
            ->andReturn($storedFile);

        $data = [
            'identifier' => 'ABC123',
            'description' => 'PSV-SR Licence',
            'filename' => 'PSV-SR_Licence.rtf',
            'licence' => 111,
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isReadOnly' => true,
            'isExternal' => false,
            'size' => null,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null,
            'isScan' => 0,
            'issuedDate' => null
        ];
        $result1 = new Result();
        $result1->addMessage('Document dispatched');
        $this->expectedSideEffect(DispatchDocument::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Document generated',
                'Document dispatched'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandPsv()
    {
        $command = Cmd::create(['id' => 111]);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->shouldReceive('isGoods')->andReturn(false);
        $licence->shouldReceive('isSpecialRestricted')->andReturn(false);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $storedFile = m::mock(File::class);
        $storedFile->shouldReceive('getIdentifier')
            ->andReturn('ABC123');

        $this->mockedSmServices['DocumentGenerator']->shouldReceive('generateAndStore')
            ->once()
            ->with('PSV_LICENCE_V1', ['licence' => 111])
            ->andReturn($storedFile);

        $data = [
            'identifier' => 'ABC123',
            'description' => 'PSV Licence',
            'filename' => 'PSV_Licence.rtf',
            'licence' => 111,
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isReadOnly' => true,
            'isExternal' => false,
            'size' => null,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null,
            'isScan' => 0,
            'issuedDate' => null
        ];
        $result1 = new Result();
        $result1->addMessage('Document dispatched');
        $this->expectedSideEffect(DispatchDocument::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Document generated',
                'Document dispatched'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
