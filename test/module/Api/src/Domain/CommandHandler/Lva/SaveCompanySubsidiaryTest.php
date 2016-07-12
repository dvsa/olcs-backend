<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Lva;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command as DomainCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Lva\SaveCompanySubsidiary;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * @covers Dvsa\Olcs\Api\Domain\CommandHandler\Lva\SaveCompanySubsidiary
 */
class SaveCompanySubsidiaryTest extends CommandHandlerTestCase
{
    const ID = 1111;
    const LICENCE_ID = 2222;
    const VERSION = 99;

    /** @var  TestSaveCompanySubsidiary */
    protected $sut;
    /** @var  m\MockInterface */
    private $mockLicenceEntity;

    public function setUp()
    {
        $this->sut = new TestSaveCompanySubsidiary();

        //  mock entities
        $this->mockLicenceEntity = m::mock(Entity\Licence\Licence::class);

        //  mock repositories
        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('CompanySubsidiary', Repository\CompanySubsidiary::class);

        parent::setUp();
    }

    public function testCreate()
    {
        $data = [
            'name' => 'unit_OrgName',
            'companyNo' => 'unit_123456798',
        ];
        $command = TransferCmd\Application\CreateCompanySubsidiary::create($data);

        //  mock db
        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->once()
            ->with(self::LICENCE_ID)
            ->andReturn($this->mockLicenceEntity);

        $this->repoMap['CompanySubsidiary']
            ->shouldReceive('save')
            ->once()
            ->andReturnUsing(
                function (Entity\Organisation\CompanySubsidiary $entity) {
                    static::assertSame($this->mockLicenceEntity, $entity->getLicence());
                    static::assertEquals('unit_OrgName', $entity->getName());
                    static::assertEquals('unit_123456798', $entity->getCompanyNo());

                    $entity->setId(self::ID);
                }
            );

        //  call
        $actual = $this->sut->create($command, self::LICENCE_ID);

        static::assertEquals(
            [
                'id' => [
                    'companySubsidiary' => self::ID,
                ],
                'messages' => [
                    'Company Subsidiary created',
                ],
            ],
            $actual->toArray()
        );
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'unit_OrgName',
            'companyNo' => 'unit_123456798',
            'version' => self::VERSION,
        ];
        $command = TransferCmd\Application\UpdateCompanySubsidiary::create($data);

        //  mock db
        $mockSubsidiaryEntity = m::mock(Entity\Organisation\CompanySubsidiary::class)
            ->shouldReceive('setName')->once()->with('unit_OrgName')->andReturnSelf()
            ->shouldReceive('setCompanyNo')->once()->with('unit_123456798')->andReturnSelf()
            ->shouldReceive('getVersion')->once()->with()->andReturn(self::VERSION + 1)
            ->getMock();

        $this->repoMap['CompanySubsidiary']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, self::VERSION)
            ->andReturn($mockSubsidiaryEntity)
            //
            ->shouldReceive('save')->once()->with($mockSubsidiaryEntity);

        //  call
        $actual = $this->sut->update($command);

        static::assertEquals(
            [
                'id' => [],
                'messages' => [
                    'Company Subsidiary updated',
                ],
            ],
            $actual->toArray()
        );
        static::assertTrue($actual->getFlag('hasChanged'));
    }
}

/**
 * Class for testing Abstract Class
 */
class TestSaveCompanySubsidiary extends SaveCompanySubsidiary
{
    /**
     * @inheritdoc
     *
     * @return DomainCmd\Result
     */
    public function create($command, $licenceId)
    {
        return parent::create($command, $licenceId);
    }

    /**
     * @inheritdoc
     *
     * @return DomainCmd\Result
     */
    public function update($command)
    {
        return parent::update($command);
    }

    /**
     * @inheritdoc
     */
    public function handleCommand(CommandInterface $command)
    {
    }
}
