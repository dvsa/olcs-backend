<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;


use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Validation\Validators\IsLicenceSurrenderable;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;

class IsLicenceSurrenderableTest extends AbstractValidatorsTestCase
{
    /**
     * @var IsLicenceSurrenderable
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new IsLicenceSurrenderable();

        parent::setUp();
    }

    public function testValidLicenceNoExistingSurrenderNoOpenApplications()
    {
        $licenceId = 1;
        $licenceStatus = Licence::LICENCE_STATUS_VALID;
        $existingSurrender = [];
        $openApplications = [];
        $expected = true;

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getStatus->getId')->andReturn($licenceStatus);
        $licenceRepo = $this->mockRepo('Licence');
        $licenceRepo->shouldReceive('fetchById')->with($licenceId)->andReturn($licence);

        $surrenderRepo = $this->mockRepo('Surrender');
        $surrenderRepo->shouldReceive('fetchByLicenceId')->with($licenceId)->andReturn($existingSurrender);

        $applicationRepo = $this->mockRepo('Application');
        $applicationRepo->shouldReceive('fetchOpenApplicationsForLicence')->with($licenceId)->andReturn($openApplications);

        $this->assertEquals($expected, $this->sut->isValid($licenceId));
    }

    public function testValidLicenceNoExistingSurrenderWithOpenApplications()
    {
        $licenceId = 1;
        $licenceStatus = Licence::LICENCE_STATUS_VALID;
        $existingSurrender = [];
        $openApplications = ['open application'];

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getStatus->getId')->andReturn($licenceStatus);
        $licenceRepo = $this->mockRepo('Licence');
        $licenceRepo->shouldReceive('fetchById')->with($licenceId)->andReturn($licence);

        $surrenderRepo = $this->mockRepo('Surrender');
        $surrenderRepo->shouldReceive('fetchByLicenceId')->with($licenceId)->andReturn($existingSurrender);

        $applicationRepo = $this->mockRepo('Application');
        $applicationRepo->shouldReceive('fetchOpenApplicationsForLicence')->with($licenceId)->andReturn($openApplications);

        $this->expectException(ForbiddenException::class);

        $this->sut->isValid($licenceId);
    }

    public function testValidLicenceWithExistingSurrender()
    {
        $licenceId = 1;
        $licenceStatus = Licence::LICENCE_STATUS_VALID;
        $existingSurrender = ['existing surrender'];

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getStatus->getId')->andReturn($licenceStatus);
        $licenceRepo = $this->mockRepo('Licence');
        $licenceRepo->shouldReceive('fetchById')->with($licenceId)->andReturn($licence);

        $surrenderRepo = $this->mockRepo('Surrender');
        $surrenderRepo->shouldReceive('fetchByLicenceId')->with($licenceId)->andReturn($existingSurrender);

        $this->expectException(ForbiddenException::class);

        $this->sut->isValid($licenceId);
    }

    public function testNotValidLicence()
    {
        $licenceId = 1;
        $licenceStatus = Licence::LICENCE_STATUS_REVOKED;

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getStatus->getId')->andReturn($licenceStatus);
        $licence->shouldReceive('getStatus->getDescription')->once();
        $licenceRepo = $this->mockRepo('Licence');
        $licenceRepo->shouldReceive('fetchById')->with($licenceId)->andReturn($licence);

        $this->expectException(ForbiddenException::class);

        $this->sut->isValid($licenceId);
    }

    public function testNotExistingLicence()
    {
        $licenceId = 1;
        $licenceRepo = $this->mockRepo('Licence');
        $licenceRepo->shouldReceive('fetchById')->with($licenceId)->andThrow(BadRequestException::class);

        $this->expectException(BadRequestException::class);

        $this->sut->isValid($licenceId);
    }

}
