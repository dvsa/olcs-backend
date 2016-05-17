<?php

/**
 * Copy document test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Transfer\Command\Document\CopyDocument as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\CopyDocument as CommandHandler;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\BusRegSearchView as BusRegSearchViewRepo;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Api\Domain\Repository\TransportManager as TransportManagerRepo;
use Dvsa\Olcs\Api\Domain\Repository\Publication as PublicationRepo;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\View\BusRegSearchView as BusRegSearchViewEntity;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific as CreateDocumentSpecificCmd;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;

/**
 * Copy document test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CopyDocumentTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Document', DocumentRepo::class);
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('BusRegSearchView', BusRegSearchViewRepo::class);
        $this->mockRepo('Cases', CasesRepo::class);
        $this->mockRepo('Organisation', OrganisationRepo::class);
        $this->mockRepo('TransportManager', TransportManagerRepo::class);
        $this->mockRepo('Publication', PublicationRepo::class);

        parent::setUp();
    }

    public function testHandleCommandWithAppplication()
    {
        $data = [
            'targetId' => 1,
            'type'     => CommandHandler::APP,
            'ids'      => [2]
        ];
        $command = Cmd::create($data);

        $mockEntity = m::mock()
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(3)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $this->repoMap['Application']
            ->shouldReceive('fetchWithLicence')
            ->with(1)
            ->andReturn($mockEntity)
            ->once()
            ->getMock();

        $mockDocument = $this->getMockDocument();

        $this->repoMap['Document']
            ->shouldReceive('fetchById')
            ->with(2)
            ->andReturn($mockDocument)
            ->once()
            ->getMock();

        $createResult = new Result();
        $createResult->addId('document', 111);
        $createResult->addMessage('Document(s) copied');

        $params = [
            'identifier' => 'identifier',
            'description' => 'description',
            'category' => 4,
            'subCategory' => 5,
            'issuedDate' => '2015-01-01',
            'filename' => 'filename',
            'isScan' => true,
            'isExternal' => true,
            'application' => 1,
            'licence' => 3,
            'size' => null,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null
        ];
        $this->expectedSideEffect(CreateDocumentSpecificCmd::class, $params, $createResult);

        $result = $this->sut->handleCommand($command);
        $res = $result->toArray();
        $this->assertEquals(111, $res['id']['document111']);
        $this->assertEquals('Document(s) copied', $res['messages'][0]);
    }

    public function testHandleCommandWithLicence()
    {
        $data = [
            'targetId' => 1,
            'type'     => CommandHandler::LIC,
            'ids'      => [2]
        ];
        $command = Cmd::create($data);

        $mockEntity = m::mock()
            ->shouldReceive('getId')
            ->andReturn(3)
            ->once()
            ->getMock();

        $this->repoMap['Licence']
            ->shouldReceive('fetchByLicNo')
            ->with(1)
            ->andReturn($mockEntity)
            ->once()
            ->getMock();

        $mockDocument = $this->getMockDocument();

        $this->repoMap['Document']
            ->shouldReceive('fetchById')
            ->with(2)
            ->andReturn($mockDocument)
            ->once()
            ->getMock();

        $createResult = new Result();
        $createResult->addId('document', 111);
        $createResult->addMessage('Document(s) copied');

        $params = [
            'identifier' => 'identifier',
            'description' => 'description',
            'category' => 4,
            'subCategory' => 5,
            'issuedDate' => '2015-01-01',
            'filename' => 'filename',
            'isScan' => true,
            'isExternal' => true,
            'application' => null,
            'licence' => 3,
            'size' => null,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null
        ];
        $this->expectedSideEffect(CreateDocumentSpecificCmd::class, $params, $createResult);

        $result = $this->sut->handleCommand($command);
        $res = $result->toArray();
        $this->assertEquals(111, $res['id']['document111']);
        $this->assertEquals('Document(s) copied', $res['messages'][0]);
    }

    public function testHandleCommandWithBusReg()
    {
        $data = [
            'targetId' => 1,
            'type'     => CommandHandler::BUSREG,
            'ids'      => [2]
        ];
        $command = Cmd::create($data);

        $mockEntity = m::mock(BusRegSearchViewEntity::class)
            ->shouldReceive('getLicId')
            ->andReturn(3)
            ->once()
            ->shouldReceive('getId')
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->repoMap['BusRegSearchView']
            ->shouldReceive('fetchByRegNo')
            ->with(1)
            ->andReturn($mockEntity)
            ->once()
            ->getMock();

        $mockDocument = $this->getMockDocument();

        $this->repoMap['Document']
            ->shouldReceive('fetchById')
            ->with(2)
            ->andReturn($mockDocument)
            ->once()
            ->getMock();

        $createResult = new Result();
        $createResult->addId('document', 111);
        $createResult->addMessage('Document(s) copied');

        $params = [
            'identifier' => 'identifier',
            'description' => 'description',
            'category' => 4,
            'subCategory' => 5,
            'issuedDate' => '2015-01-01',
            'filename' => 'filename',
            'isScan' => true,
            'isExternal' => true,
            'application' => null,
            'licence' => 3,
            'size' => null,
            'busReg' => 1,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null
        ];
        $this->expectedSideEffect(CreateDocumentSpecificCmd::class, $params, $createResult);

        $result = $this->sut->handleCommand($command);
        $res = $result->toArray();
        $this->assertEquals(111, $res['id']['document111']);
        $this->assertEquals('Document(s) copied', $res['messages'][0]);
    }

    public function testHandleCommandWithCases()
    {
        $data = [
            'targetId' => 1,
            'type'     => CommandHandler::CASES,
            'ids'      => [2]
        ];
        $command = Cmd::create($data);

        $mockEntity = m::mock()
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(33)
                ->once()
                ->getMock()
            )
            ->twice()
            ->shouldReceive('getApplication')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getId')
                    ->andReturn(44)
                    ->once()
                    ->getMock()
            )
            ->twice()
            ->shouldReceive('getTransportManager')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getId')
                    ->andReturn(55)
                    ->once()
                    ->getMock()
            )
            ->twice()
            ->getMock();

        $this->repoMap['Cases']
            ->shouldReceive('fetchExtended')
            ->with(1)
            ->andReturn($mockEntity)
            ->once()
            ->getMock();

        $mockDocument = $this->getMockDocument();

        $this->repoMap['Document']
            ->shouldReceive('fetchById')
            ->with(2)
            ->andReturn($mockDocument)
            ->once()
            ->getMock();

        $createResult = new Result();
        $createResult->addId('document', 111);
        $createResult->addMessage('Document(s) copied');

        $params = [
            'identifier' => 'identifier',
            'description' => 'description',
            'category' => 4,
            'subCategory' => 5,
            'issuedDate' => '2015-01-01',
            'filename' => 'filename',
            'isScan' => true,
            'isExternal' => true,
            'application' => 44,
            'licence' => 33,
            'size' => null,
            'busReg' => null,
            'case' => 1,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => 55,
            'operatingCentre' => null,
            'opposition' => null
        ];
        $this->expectedSideEffect(CreateDocumentSpecificCmd::class, $params, $createResult);

        $result = $this->sut->handleCommand($command);
        $res = $result->toArray();
        $this->assertEquals(111, $res['id']['document111']);
        $this->assertEquals('Document(s) copied', $res['messages'][0]);
    }

    public function testHandleCommandWithIrfo()
    {
        $data = [
            'targetId' => 1,
            'type'     => CommandHandler::IRFO,
            'ids'      => [2]
        ];
        $command = Cmd::create($data);

        $mockEntity = m::mock();

        $this->repoMap['Organisation']
            ->shouldReceive('fetchById')
            ->with(1)
            ->andReturn($mockEntity)
            ->once()
            ->getMock();

        $mockDocument = $this->getMockDocument();

        $this->repoMap['Document']
            ->shouldReceive('fetchById')
            ->with(2)
            ->andReturn($mockDocument)
            ->once()
            ->getMock();

        $createResult = new Result();
        $createResult->addId('document', 111);
        $createResult->addMessage('Document(s) copied');

        $params = [
            'identifier' => 'identifier',
            'description' => 'description',
            'category' => 4,
            'subCategory' => 5,
            'issuedDate' => '2015-01-01',
            'filename' => 'filename',
            'isScan' => true,
            'isExternal' => true,
            'application' => null,
            'licence' => null,
            'size' => null,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => 1,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null
        ];
        $this->expectedSideEffect(CreateDocumentSpecificCmd::class, $params, $createResult);

        $result = $this->sut->handleCommand($command);
        $res = $result->toArray();
        $this->assertEquals(111, $res['id']['document111']);
        $this->assertEquals('Document(s) copied', $res['messages'][0]);
    }

    public function testHandleCommandWithTransportManager()
    {
        $data = [
            'targetId' => 1,
            'type'     => CommandHandler::TM,
            'ids'      => [2]
        ];
        $command = Cmd::create($data);

        $mockEntity = m::mock();

        $this->repoMap['TransportManager']
            ->shouldReceive('fetchById')
            ->with(1)
            ->andReturn($mockEntity)
            ->once()
            ->getMock();

        $mockDocument = $this->getMockDocument();

        $this->repoMap['Document']
            ->shouldReceive('fetchById')
            ->with(2)
            ->andReturn($mockDocument)
            ->once()
            ->getMock();

        $createResult = new Result();
        $createResult->addId('document', 111);
        $createResult->addMessage('Document(s) copied');

        $params = [
            'identifier' => 'identifier',
            'description' => 'description',
            'category' => 4,
            'subCategory' => 5,
            'issuedDate' => '2015-01-01',
            'filename' => 'filename',
            'isScan' => true,
            'isExternal' => true,
            'application' => null,
            'licence' => null,
            'size' => null,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => 1,
            'operatingCentre' => null,
            'opposition' => null
        ];
        $this->expectedSideEffect(CreateDocumentSpecificCmd::class, $params, $createResult);

        $result = $this->sut->handleCommand($command);
        $res = $result->toArray();
        $this->assertEquals(111, $res['id']['document111']);
        $this->assertEquals('Document(s) copied', $res['messages'][0]);
    }

    /**
     * Tests copying a publication document
     */
    public function testHandleCommandWithPublication()
    {
        $data = [
            'targetId' => 1,
            'type'     => CommandHandler::PUBLICATION,
            'ids'      => [2]
        ];
        $command = Cmd::create($data);

        $mockEntity = m::mock(PublicationEntity::class);

        $mockTa = m::mock(TrafficAreaEntity::class);
        $mockTa->shouldReceive('getId')->once()->andReturn(6);

        $this->repoMap['Publication']
            ->shouldReceive('fetchById')
            ->with(1)
            ->andReturn($mockEntity)
            ->once()
            ->getMock();

        $mockDocument = $this->getMockDocument($mockTa);

        $this->repoMap['Document']
            ->shouldReceive('fetchById')
            ->with(2)
            ->andReturn($mockDocument)
            ->once()
            ->getMock();

        $createResult = new Result();
        $createResult->addId('document', 111);
        $createResult->addMessage('Document(s) copied');

        $params = [
            'identifier' => 'identifier',
            'description' => 'description',
            'category' => 4,
            'subCategory' => 5,
            'issuedDate' => '2015-01-01',
            'filename' => 'filename',
            'isScan' => true,
            'isExternal' => true,
            'application' => null,
            'licence' => null,
            'size' => null,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => 6,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null
        ];
        $this->expectedSideEffect(CreateDocumentSpecificCmd::class, $params, $createResult);

        $result = $this->sut->handleCommand($command);
        $res = $result->toArray();
        $this->assertEquals(111, $res['id']['document111']);
        $this->assertEquals('Document(s) copied', $res['messages'][0]);
    }

    public function testHandleCommandWithWrrongType()
    {
        $this->setExpectedException(ValidationException::class);

        $data = [
            'targetId' => 1,
            'type'     => 'wrong',
            'ids'      => [2]
        ];
        $command = Cmd::create($data);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithInvalidEntity()
    {
        $this->setExpectedException(ValidationException::class);

        $data = [
            'targetId' => 1,
            'type'     => CommandHandler::APP,
            'ids'      => [2]
        ];
        $command = Cmd::create($data);

        $this->repoMap['Application']
            ->shouldReceive('fetchWithLicence')
            ->with(1)
            ->andThrow(NotFoundException::class)
            ->once()
            ->getMock();

        $this->sut->handleCommand($command);
    }

    protected function getMockDocument($trafficArea = null)
    {
        return m::mock()
            ->shouldReceive('getIdentifier')
            ->andReturn('identifier')
            ->once()
            ->getMock()
            ->shouldReceive('getTrafficArea')
            ->andReturn($trafficArea)
            ->once()
            ->getMock()
            ->shouldReceive('getDescription')
            ->andReturn('description')
            ->once()
            ->getMock()
            ->shouldReceive('getCategory')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(4)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock()
            ->shouldReceive('getSubCategory')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(5)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock()
            ->shouldReceive('getIssuedDate')
            ->andReturn('2015-01-01')
            ->once()
            ->getMock()
            ->shouldReceive('getFilename')
            ->andReturn('filename')
            ->once()
            ->getMock()
            ->shouldReceive('getIsScan')
            ->andReturn(true)
            ->once()
            ->getMock()
            ->shouldReceive('getIsExternal')
            ->andReturn(true)
            ->once()
            ->getMock();
    }
}
