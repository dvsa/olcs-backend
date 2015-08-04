<?php

/**
 * DeletePeopleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Repository\Licence as  LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationPerson as  OrganisationPersonRepo;
use Dvsa\Olcs\Api\Domain\Repository\Person as  PersonRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\DeletePeople as CommandHandler;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\Licence\DeletePeople as Command;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CreateGoodsVehicle as VehicleCmd;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CreateGoodsDiscs as CreateGoodsDiscsCmd;

/**
 * DeletePeopleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DeletePeopleTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('OrganisationPerson', OrganisationPersonRepo::class);
        $this->mockRepo('Person', PersonRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'title_mr', 'org_t_llp', 'org_t_p'
        ];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 52,
            'personIds' => [543,63]
        ];
        $command = Command::create($data);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($this->refData['org_t_p']);
        $organisation->setId(48);
        $licence = new LicenceEntity($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());
        $op1 = new \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson();
        $p1 = (new \Dvsa\Olcs\Api\Entity\Person\Person())->setId(185);
        $op1->setId(3)->setPerson($p1);
        $op2 = new \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson();
        $op2->setId(54);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($licence);

        $this->repoMap['OrganisationPerson']->shouldReceive('fetchListForOrganisationAndPerson')
            ->with(48, 543)->once()->andReturn([$op1]);
        $this->repoMap['OrganisationPerson']->shouldReceive('delete')->with($op1)->once()->andReturn();
        $this->repoMap['OrganisationPerson']->shouldReceive('fetchListForPerson')->with(543)->once()->andReturn([]);
        $this->repoMap['Person']->shouldReceive('delete')->with($p1)->once()->andReturn([]);

        $this->repoMap['OrganisationPerson']->shouldReceive('fetchListForOrganisationAndPerson')
            ->with(48, 63)->once()->andReturn([$op2]);
        $this->repoMap['OrganisationPerson']->shouldReceive('delete')->with($op2)->once()->andReturn();
        $this->repoMap['OrganisationPerson']->shouldReceive('fetchListForPerson')->with(63)->once()->andReturn(['X']);

        $response = $this->sut->handleCommand($command);

        $this->assertSame(
            [
                "OrganisatonPerson ID {$op1->getId()} deleted",
                "Person ID {$p1->getId()} deleted",
                "OrganisatonPerson ID {$op2->getId()} deleted",
            ],
            $response->getMessages()
        );
    }
}
