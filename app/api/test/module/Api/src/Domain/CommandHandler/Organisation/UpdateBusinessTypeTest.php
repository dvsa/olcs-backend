<?php

/**
 * Update Business Type Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Organisation\UpdateBusinessType;
use Dvsa\Olcs\Api\Domain\Repository\Organisation;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Organisation\UpdateBusinessType as Cmd;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Update Business Type Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateBusinessTypeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateBusinessType();
        $this->mockRepo('Organisation', Organisation::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY,
            OrganisationEntity::ORG_TYPE_SOLE_TRADER
        ];

        parent::initReferences();
    }

    public function testHandleCommandWhenCanChangeWithoutValue()
    {
        $this->setExpectedException(ValidationException::class);

        $data = [
            'id' => 11,
            'version' => 1,
            'application' => 111
        ];

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(11);

        $organisation->shouldReceive('hasInforceLicences')
            ->andReturn(false);

        $command = Cmd::create($data);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($organisation);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWhenCanChangeWithoutChange()
    {
        $data = [
            'id' => 11,
            'version' => 1,
            'application' => 111,
            'businessType' => OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY
        ];

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(11);
        $organisation->setType($this->refData[OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY]);

        $organisation->shouldReceive('hasInforceLicences')
            ->andReturn(false);

        $command = Cmd::create($data);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($organisation);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Business type unchanged'
            ]
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWhenCantChangeWithChange1()
    {
        $this->setExpectedException(ValidationException::class);

        $data = [
            'id' => 11,
            'version' => 1,
            'licence' => 111,
            'businessType' => OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY
        ];

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(11);
        $organisation->setType($this->refData[OrganisationEntity::ORG_TYPE_SOLE_TRADER]);

        $command = Cmd::create($data);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($organisation);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWhenCantChangeWithChange2()
    {
        $this->setExpectedException(ValidationException::class);

        $data = [
            'id' => 11,
            'version' => 1,
            'variation' => 111,
            'businessType' => OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY
        ];

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(11);
        $organisation->setType($this->refData[OrganisationEntity::ORG_TYPE_SOLE_TRADER]);

        $command = Cmd::create($data);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($organisation);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWhenCantChangeWithChange3()
    {
        $this->setExpectedException(ValidationException::class);

        $data = [
            'id' => 11,
            'version' => 1,
            'application' => 111,
            'businessType' => OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY
        ];

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(11);
        $organisation->setType($this->refData[OrganisationEntity::ORG_TYPE_SOLE_TRADER]);

        $organisation->shouldReceive('hasInforceLicences')
            ->andReturn(true);

        $command = Cmd::create($data);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($organisation);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWhenCantChangeWithoutChangeWithoutApplication1()
    {
        $data = [
            'id' => 11,
            'version' => 1,
            'licence' => 111,
            'businessType' => OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY
        ];

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(11);
        $organisation->setType($this->refData[OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY]);

        $command = Cmd::create($data);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($organisation);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Can\'t update business type'
            ]
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWhenCantChangeWithoutChangeWithoutApplication2()
    {
        $data = [
            'id' => 11,
            'version' => 1,
            'licence' => 111
        ];

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(11);
        $organisation->setType($this->refData[OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY]);

        $command = Cmd::create($data);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($organisation);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Can\'t update business type'
            ]
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWhenCantChangeWithoutChangeWithApplication()
    {
        $data = [
            'id' => 11,
            'version' => 1,
            'application' => 111,
            'businessType' => OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY
        ];

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(11);
        $organisation->setType($this->refData[OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY]);

        $organisation->shouldReceive('hasInforceLicences')
            ->andReturn(true);

        $command = Cmd::create($data);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($organisation);

        $result1 = new Result();
        $result1->addMessage('Section updated');

        $completionData = ['id' => 111, 'section' => 'businessType'];
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $completionData, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Can\'t update business type',
                'Section updated'
            ]
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWhenCanChangeWithChange()
    {
        $data = [
            'id' => 11,
            'version' => 1,
            'application' => 111,
            'businessType' => OrganisationEntity::ORG_TYPE_SOLE_TRADER
        ];

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(11);
        $organisation->setType($this->refData[OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY]);

        $organisation->shouldReceive('hasInforceLicences')
            ->andReturn(false);

        $command = Cmd::create($data);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($organisation)
            ->shouldReceive('beginTransaction')
            ->once()
            ->shouldReceive('save')
            ->once()
            ->with($organisation)
            ->shouldReceive('commit')
            ->once();

        $this->assertSame($this->refData[OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY], $organisation->getType());

        $result1 = new Result();
        $result1->addMessage('Section updated');

        $completionData = ['id' => 111, 'section' => 'businessType'];
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $completionData, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Business type updated',
                'Section updated'
            ]
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());

        $this->assertSame($this->refData[OrganisationEntity::ORG_TYPE_SOLE_TRADER], $organisation->getType());
    }

    public function testHandleCommandWhenCanChangeWithChangeWithException()
    {
        $this->setExpectedException(\Exception::class);

        $data = [
            'id' => 11,
            'version' => 1,
            'application' => 111,
            'businessType' => OrganisationEntity::ORG_TYPE_SOLE_TRADER
        ];

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(11);
        $organisation->setType($this->refData[OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY]);

        $organisation->shouldReceive('hasInforceLicences')
            ->andReturn(false);

        $command = Cmd::create($data);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($organisation)
            ->shouldReceive('beginTransaction')
            ->once()
            ->shouldReceive('save')
            ->once()
            ->with($organisation)
            ->shouldReceive('commit')
            ->once()
            ->andThrow(\Exception::class)
            ->shouldReceive('rollback')
            ->once();

        $this->assertSame($this->refData[OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY], $organisation->getType());

        $result1 = new Result();
        $result1->addMessage('Section updated');

        $completionData = ['id' => 111, 'section' => 'businessType'];
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $completionData, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Business type updated',
                'Section updated'
            ]
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());

        $this->assertSame($this->refData[OrganisationEntity::ORG_TYPE_SOLE_TRADER], $organisation->getType());
    }
}
