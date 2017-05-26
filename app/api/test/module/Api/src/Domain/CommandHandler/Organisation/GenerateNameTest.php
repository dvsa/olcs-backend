<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Organisation;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\CommandHandler\Organisation\GenerateName;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\CommandHandler\Organisation\GenerateName
 */
class GenerateNameTest extends CommandHandlerTestCase
{
    const PERSON_ID = 8001;
    const APP_ID = 9001;
    const LIC_ID = 7001;

    /** @var  GenerateName */
    protected $sut;

    /** @var  m\MockInterface */
    private $mockAppRepo;
    /** @var  m\MockInterface */
    private $mockLicRepo;
    /** @var  Repository\Organisation | m\MockInterface */
    private $mockOrgRepo;

    /** @var  Entity\Organisation\Organisation | m\MockInterface */
    private $mockOrg;
    /** @var  Entity\Licence\Licence | m\MockInterface */
    private $mockLic;
    /** @var  Entity\Application\Application | m\MockInterface */
    private $mockApp;

    public function setUp()
    {
        $this->sut = new GenerateName();

        //  mock Entity
        $this->mockOrg = m::mock(Entity\Organisation\Organisation::class)->makePartial();

        $this->mockLic = m::mock(Entity\Licence\Licence::class)->makePartial();
        $this->mockLic
            ->setId(self::LIC_ID)
            ->setOrganisation($this->mockOrg);

        $this->mockApp = m::mock(Entity\Application\Application::class)->makePartial();
        $this->mockApp
            ->setId(self::APP_ID)
            ->setLicence($this->mockLic);

        //  mock Repos
        $this->mockAppRepo = $this->mockRepo('Application', Repository\Application::class);
        $this->mockAppRepo->shouldReceive('fetchById')->with(self::APP_ID)->andReturn($this->mockApp);

        $this->mockLicRepo = $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockLicRepo->shouldReceive('fetchById')->with(self::LIC_ID)->andReturn($this->mockLic);

        $this->mockOrgRepo = $this->mockRepo('Organisation', Repository\Organisation::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            Entity\Organisation\Organisation::ORG_TYPE_REGISTERED_COMPANY,
            Entity\Organisation\Organisation::ORG_TYPE_LLP,
            Entity\Organisation\Organisation::ORG_TYPE_PARTNERSHIP,
            Entity\Organisation\Organisation::ORG_TYPE_SOLE_TRADER,
        ];

        parent::initReferences();
    }

    public function testGenerateNameNull()
    {
        $cmd = TransferCmd\Organisation\GenerateName::create(['licence' => self::LIC_ID]);

        $this->mockOrg
            ->setType($this->refData[Entity\Organisation\Organisation::ORG_TYPE_SOLE_TRADER])
            ->setOrganisationPersons(new ArrayCollection());

        $this->mockOrgRepo->shouldReceive('save')->never();

        $actual = $this->sut->handleCommand($cmd);

        static::assertEquals(['Unable to generate name'], $actual->getMessages());
    }

    /**
     * @dataProvider dpTestGenerateName
     */
    public function testGenerateName($type, $orgPersons, $expect)
    {
        $cmd = TransferCmd\Organisation\GenerateName::create(['licence' => self::LIC_ID]);

        $this->mockOrg
            ->setType($this->refData[$type])
            ->setOrganisationPersons(new ArrayCollection($orgPersons));

        $this->mockOrgRepo
            ->shouldReceive('save')
            ->andReturnUsing(
                function (Entity\Organisation\Organisation $org) use ($expect) {
                    static::assertEquals($expect, $org->getName());
                }
            );

        $this->sut->handleCommand($cmd);
    }

    public function dpTestGenerateName()
    {
        return [
            'ST' => [
                'type' => Entity\Organisation\Organisation::ORG_TYPE_SOLE_TRADER,
                'orgPersons' => [
                    $this->getMockOrgPerson('unit_Fist', 'unit_Last'),
                ],
                'expect' => 'unit_Fist unit_Last',
            ],
            'Partner x 1' => [
                'type' => Entity\Organisation\Organisation::ORG_TYPE_PARTNERSHIP,
                'orgPersons' => [
                    $this->getMockOrgPerson('unit_Fist', 'unit_Last'),
                ],
                'expect' => 'unit_Fist unit_Last',
            ],
            'Partner x 2' => [
                'type' => Entity\Organisation\Organisation::ORG_TYPE_PARTNERSHIP,
                'orgPersons' => [
                    $this->getMockOrgPerson('unit_Fist', 'unit_Last'),
                    $this->getMockOrgPerson('unit_Fist2', 'unit_Last2'),
                ],
                'expect' => 'unit_Fist unit_Last & unit_Fist2 unit_Last2',
            ],
            'Partner > 2' => [
                'type' => Entity\Organisation\Organisation::ORG_TYPE_PARTNERSHIP,
                'orgPersons' => [
                    $this->getMockOrgPerson('unit_Fist', 'unit_Last'),
                    $this->getMockOrgPerson('unit_Fist2', 'unit_Last2'),
                    $this->getMockOrgPerson('unit_Fist3', 'unit_Last3'),
                ],
                'expect' => 'unit_Fist unit_Last & Partners',
            ],
        ];
    }

    private function getMockOrgPerson($firstName, $lastName)
    {
        $mockPerson = new Entity\Person\Person();
        $mockPerson
            ->setForename($firstName)
            ->setFamilyName($lastName);

        $mockRel = new Entity\Organisation\OrganisationPerson();
        $mockRel->setPerson($mockPerson);

        return $mockRel;
    }

    /**
     * @dataProvider dpTestHandleOk
     */
    public function testHandleOk($cmdData)
    {
        $cmd = TransferCmd\Organisation\GenerateName::create($cmdData);

        $orgPersons = new ArrayCollection(
            [
                $this->getMockOrgPerson('unit_First', 'unit_Last'),
            ]
        );
        $this->mockOrg
            ->setType($this->refData[Entity\Organisation\Organisation::ORG_TYPE_SOLE_TRADER])
            ->setOrganisationPersons($orgPersons);

        $this->mockOrgRepo
            ->shouldReceive('save')
            ->andReturnUsing(
                function (Entity\Organisation\Organisation $org) {
                    static::assertEquals('unit_First unit_Last', $org->getName());
                }
            );

        $actual = $this->sut->handleCommand($cmd);

        static::assertEquals(['Name succesfully generated'], $actual->getMessages());
    }

    public function dpTestHandleOk()
    {
        return [
            [
                'cmdData' => ['application' => self::APP_ID],
            ],
            [
                'cmdData' => ['licence' => self::LIC_ID],
            ],
        ];
    }

    public function testHandleFailBadData()
    {
        $this->setExpectedException(BadRequestException::class, GenerateName::ERR_INVALID_DATA);

        $this->sut->handleCommand(
            TransferCmd\Organisation\GenerateName::create([])
        );
    }

    public function testHandleFailAppIsVariation()
    {
        $this->setExpectedException(ValidationException::class, GenerateName::ERR_ONLY_NEW_APP);

        $this->mockApp->setIsVariation(true);

        $this->sut->handleCommand(
            TransferCmd\Organisation\GenerateName::create(['application' => self::APP_ID])
        );
    }

    public function testHandleFailOrgInvalid()
    {
        $this->setExpectedException(ValidationException::class, GenerateName::ERR_ORG_INVALID);

        $this->mockLic->setOrganisation(null);

        $this->sut->handleCommand(
            TransferCmd\Organisation\GenerateName::create(['licence' => self::LIC_ID])
        );
    }

    public function testHandleFailNotSoleTraiderOrPartnership()
    {
        $this->setExpectedException(ValidationException::class, GenerateName::ERR_ORG_TYPE_INVALID);

        $this->mockOrg->setType($this->refData[Entity\Organisation\Organisation::ORG_TYPE_REGISTERED_COMPANY]);

        $this->sut->handleCommand(
            TransferCmd\Organisation\GenerateName::create(['licence' => self::LIC_ID])
        );
    }
}
