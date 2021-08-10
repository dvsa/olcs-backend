<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Tm;

use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails;
use Dvsa\Olcs\Api\Domain\Repository\TransportManager;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Dvsa\OlcsTest\Api\Domain\Repository\RepositoryMockBuilder;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Tm\Update;
use Dvsa\Olcs\Api\Domain\Repository\TransportManager as TransportManagerRepo;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails as ContactDetailsRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Tm\Update as Cmd;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress as SaveAddressCmd;
use Dvsa\Olcs\Api\Domain\Command\Person\UpdateFull as UpdatePersonCmd;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Mockery\MockInterface;

class UpdateTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Update();
        $this->mockRepo('TransportManager', TransportManagerRepo::class);
        $this->mockRepo('ContactDetails', ContactDetailsRepo::class);

        $this->mockedSmServices = [
            CacheEncryption::class => m::mock(CacheEncryption::class),
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            TransportManagerEntity::TRANSPORT_MANAGER_STATUS_CURRENT,
            TransportManagerEntity::TRANSPORT_MANAGER_TYPE_BOTH
        ];

        parent::initReferences();
    }

    /**
     * @test
     */
    public function handleCommand_IsCallable()
    {
        $this->assertIsCallable([$this->sut, 'handleCommand']);
    }

    /**
     * @depends handleCommand_IsCallable
     * @test
     */
    public function handleCommand()
    {
        $id = 1;
        $data = [
            'id' => $id,
            'version' => 2,
            'type' => TransportManagerEntity::TRANSPORT_MANAGER_TYPE_BOTH,
            'status' => TransportManagerEntity::TRANSPORT_MANAGER_STATUS_CURRENT,
            'firstName' => 'fn',
            'lastName' => 'ln',
            'birthDate' => '2015-01-01',
            'birthPlace' => 'bp',
            'title' => 'title_mr',
            'emailAddress' => 'email@address.com',
            'homeCdId' => 3,
            'homeCdVersion' => 4,
            'personId' => 5,
            'personVersion' => 6,
            'homeAddressId' => 7,
            'homeAddressVersion' => 8,
            'workAddressId' => null,
            'workAddressVersion' => null,
            'homeAddressLine1' => 'hal1',
            'homeAddressLine2' => 'hal2',
            'homeAddressLine3' => 'hal3',
            'homeAddressLine4' => 'hal4',
            'homeTown' => 'ht',
            'homePostcode' => 'hpc',
            'homeCountryCode' => 'hcc',
            'workAddressLine1' => 'wal1',
            'workAddressLine2' => 'wal2',
            'workAddressLine3' => 'wal3',
            'workAddressLine4' => 'wal4',
            'workTown' => 'wt',
            'workPostcode' => 'wpc',
            'workCountryCode' => 'wcc'
        ];

        $command = Cmd::create($data);

        $personResult = new Result();
        $personResult->addId('person', $data['personId']);
        $personResult->addMessage('Person updated');
        $this->expectedSideEffect(
            UpdatePersonCmd::class,
            [
                'id'         => $data['personId'],
                'version'    => $data['personVersion'],
                'firstName'  => $data['firstName'],
                'lastName'   => $data['lastName'],
                'title'      => $data['title'],
                'birthDate'  => $data['birthDate'],
                'birthPlace' => $data['birthPlace']
            ],
            $personResult
        );

        $workAddressResult = new Result();
        $workAddressResult->setFlag('hasChanged', true);
        $workAddressResult->addId('address', 10);
        $this->expectedSideEffect(
            SaveAddressCmd::class,
            [
                'id'           => $data['workAddressId'],
                'version'      => $data['workAddressVersion'],
                'addressLine1' => $data['workAddressLine1'],
                'addressLine2' => $data['workAddressLine2'],
                'addressLine3' => $data['workAddressLine3'],
                'addressLine4' => $data['workAddressLine4'],
                'town'         => $data['workTown'],
                'postcode'     => $data['workPostcode'],
                'countryCode'  => $data['workCountryCode'],
                'contactType'  => 'ct_tm',
            ],
            $workAddressResult
        );

        $homeAddressResult = new Result();
        $homeAddressResult->setFlag('hasChanged', true);
        $this->expectedSideEffect(
            SaveAddressCmd::class,
            [
                'id'           => $data['homeAddressId'],
                'version'      => $data['homeAddressVersion'],
                'addressLine1' => $data['homeAddressLine1'],
                'addressLine2' => $data['homeAddressLine2'],
                'addressLine3' => $data['homeAddressLine3'],
                'addressLine4' => $data['homeAddressLine4'],
                'town'         => $data['homeTown'],
                'postcode'     => $data['homePostcode'],
                'countryCode'  => $data['homeCountryCode'],
            ],
            $homeAddressResult
        );

        $mockContactDetails = m::mock(ContactDetailsEntity::class)
            ->shouldReceive('setEmailAddress')
            ->with($data['emailAddress'])
            ->once()
            ->shouldReceive('getVersion')
            ->andReturn(5)
            ->once()
            ->shouldReceive('getId')
            ->andReturn($data['homeCdId'])
            ->once()
            ->getMock();

        $this->repoMap['ContactDetails']
            ->shouldReceive('fetchById')
            ->with($data['homeCdId'])
            ->andReturn($mockContactDetails)
            ->once()
            ->shouldReceive('save')
            ->with($mockContactDetails)
            ->once()
            ->getMock();

        $mockTransportManager = m::mock(TransportManagerEntity::class)
            ->shouldReceive('updateTransportManager')
            ->with(
                m::type(RefData::class),
                m::type(RefData::class),
                null
            )
            ->once()
            ->shouldReceive('getId')
            ->andReturn($id)
            ->getMock();

        $mockTransportManager = $this->expectedCacheClearFromUserCollection($mockTransportManager);

        $this->repoMap['TransportManager']
            ->shouldReceive('fetchById')
            ->with($id)
            ->andReturn($mockTransportManager)
            ->once()
            ->shouldReceive('save')
            ->with($mockTransportManager)
            ->once()
            ->getMock();

        $this->expectedQueueSideEffect($id, Queue::TYPE_UPDATE_NYSIIS_TM_NAME, ['id' => $id]);

        $result = $this->sut->handleCommand($command);

        $res = $result->toArray();
        $this->assertEquals($res['id']['transportManager'], $id);
        $this->assertEquals($res['id']['homeAddress'], $data['homeAddressId']);
        $this->assertEquals($res['id']['workAddress'], 10);
        $this->assertEquals($res['id']['homeContactDetails'], $data['homeCdId']);
        $this->assertEquals($res['id']['person'], $data['personId']);
    }

    /**
     * @test
     * @depends handleCommand_IsCallable
     */
    public function handleCommand_UpdatesHomeAddress()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $command = Cmd::create(['homeAddressId' => $expectedAddressId = 1234]);

        // Define Expectations
        $commandHandler = $this->resolveMockService($serviceLocator, 'CommandHandlerManager');
        $commandHandler->shouldReceive('handleCommand')->withArgs(function ($command) use ($expectedAddressId) {
            return $command instanceof SaveAddress && $command->getId() === $expectedAddressId;
        })->atLeast()->once()->andReturn(new Result());

        // Execute
        $sut->handleCommand($command);
    }

    /**
     * @test
     * @depends handleCommand_IsCallable
     */
    public function handleCommand_CreatesHomeAddress()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $command = Cmd::create([]);

        // Define Expectations
        $commandHandler = $this->resolveMockService($serviceLocator, 'CommandHandlerManager');
        $commandHandler->shouldReceive('handleCommand')->withArgs(function ($command) {
            return $command instanceof SaveAddress && null === $command->getId();
        })->atLeast()->once()->andReturn(new Result());

        // Execute
        $sut->handleCommand($command);
    }


    /**
     * @test
     * @depends handleCommand_IsCallable
     */
    public function handleCommand_UpdatesHomeAddress_WhenHomeAddressIdProvided()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $command = Cmd::create(['id' => $transportManagerId = 1234, 'homeAddressId' => $homeAddressId = 4321]);
        $mockTransportManager = new TransportManagerEntity();
        $this->transportManagerRepository()->shouldReceive('fetchById')->with($transportManagerId)->andReturn($mockTransportManager);

        // Define Expectations
        $commandHandler = $this->resolveMockService($serviceLocator, 'CommandHandlerManager');
        $commandHandler->shouldReceive('handleCommand')->withArgs(function ($command) use ($homeAddressId) {
            return $command instanceof SaveAddress && $command->getId() === $homeAddressId;
        })->once()->andReturn(new Result());

        // Execute
        $sut->handleCommand($command);
    }

    /**
     * @test
     * @depends handleCommand_IsCallable
     */
    public function handleCommand_DoesNotCreateHomeContactDetails()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $command = Cmd::create(['homeAddressId' => $homeAddressId = 4321]);

        // Define Expectations
        $commandHandler = $this->resolveMockService($serviceLocator, 'CommandHandlerManager');
        $commandHandler->shouldReceive('handleCommand')->withArgs(function ($command) use ($homeAddressId) {
            return $command instanceof SaveAddress && $command->getId() === $homeAddressId && null === $command->getContactType();
        })->once()->andReturn(new Result());

        // Execute
        $sut->handleCommand($command);
    }

    /**
     * @test
     * depends handleCommand_CreatesHomeAddress
     */
    public function handleCommand_CreatesHomeAddress_WhenNoHomeAddressIdProvided()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $command = Cmd::create(['homeAddressId' => null, 'workAddressId' => $workAddressId = 1234]);
        $homeAddressSaveResult = new Result();
        $homeAddressSaveResult->addId('address', $newHomeAddressId = 4321);
        $homeAddressSaveResult->setFlag('hasChanged', true);
        $commandHandler = $this->resolveMockService($serviceLocator, 'CommandHandlerManager');
        $commandHandler->shouldReceive('handleCommand')->withArgs(function ($command) {
            return $command instanceof SaveAddress && null === $command->getId();
        })->once()->andReturn($homeAddressSaveResult);

        // Define Expectation
        $this->contactDetailsRepository()->shouldReceive('save')->withArgs(function ($entity) use ($newHomeAddressId) {
            return $entity instanceof ContactDetailsEntity
                && ($address = $entity->getAddress()) instanceof Address
                && $address->getId() === $newHomeAddressId;
        })->once();

        // Execute
        $sut->handleCommand($command);
    }

    /**
     * @test
     * @depends handleCommand_UpdatesHomeAddress
     */
    public function handleCommand_ReportsNoUpdatesToHomeAddress_WhenNoChangeRequired()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $command = Cmd::create(['homeAddressId' => $homeAddressId = 1234]);
        $homeAddressSaveResult = new Result();
        $homeAddressSaveResult->setFlag('hasChanged', false);
        $commandHandler = $this->resolveMockService($serviceLocator, 'CommandHandlerManager');
        $commandHandler->shouldReceive('handleCommand')->withArgs(function ($command) use ($homeAddressId) {
            return $command instanceof SaveAddress && $command->getId() === $homeAddressId;
        })->once()->andReturn($homeAddressSaveResult);

        // Execute
        $result = $sut->handleCommand($command);

        // Assert
        $this->assertNotContains('Home address updated', $result->getMessages());
    }

    /**
     * @test
     * @depends handleCommand_CreatesHomeAddress
     */
    public function handleCommand_ReportsNoUpdatesToHomeContactDetails_WhenVersionIsUnchanged()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $contactDetailsVersion = 3;
        $command = Cmd::create(['homeCdId' => $homeContactDetailsId = 4321, 'homeCdVersion' => (string) $contactDetailsVersion]);
        $this->contactDetailsRepository()->shouldReceive('fetchById')->with($homeContactDetailsId)->andReturnUsing(function () use ($homeContactDetailsId, $contactDetailsVersion) {
            $entity = new ContactDetailsEntity(new RefData(ContactDetailsEntity::CONTACT_TYPE_TRANSPORT_MANAGER));
            $entity->setId($homeContactDetailsId);
            $entity->setVersion($contactDetailsVersion);
            return $entity;
        });

        // Execute
        $result = $sut->handleCommand($command);

        // Assert
        $this->assertNotContains('Home contact details updated', $result->getMessages());
    }

    protected function setUpRepositories(): void
    {
        $this->contactDetailsRepository();
        $this->transportManagerRepository();
    }

    /**
    * @return ContactDetails|MockInterface
    */
    protected function contactDetailsRepository(): MockInterface
    {
        $repositoryServiceManager = $this->repositoryServiceManager();
        if (! $repositoryServiceManager->has('ContactDetails')) {
            $builder = new RepositoryMockBuilder(ContactDetails::class);
            $builder->setEntityBuilder(function ($id) {
                $entity = new ContactDetailsEntity(new RefData(ContactDetailsEntity::CONTACT_TYPE_TRANSPORT_MANAGER));
                $entity->setId($id);
                return $entity;
            });
            $repositoryServiceManager->setService('ContactDetails', $builder->build());
        }
        return $repositoryServiceManager->get('ContactDetails');
    }

    /**
     * @return MockInterface|TransportManager
     */
    protected function transportManagerRepository(): MockInterface
    {
        $repositoryServiceManager = $this->repositoryServiceManager();
        if (! $repositoryServiceManager->has('TransportManager')) {
            $builder = new RepositoryMockBuilder(TransportManager::class);
            $builder->setEntityBuilder(function ($id) {
                $entity = new TransportManagerEntity();
                $entity->setId($id);
                return $entity;
            });
            $repositoryServiceManager->setService('TransportManager', $builder->build());
        }
        return $repositoryServiceManager->get('TransportManager');
    }

    protected function setUpSut()
    {
        return $this->sut->createService($this->commandHandlerManager());
    }

    /**
     * @param string $repositoryClass
     * @param string $entityClass
     * @return MockInterface
     */
    protected function setUpMockRepository(string $repositoryClass, string $entityClass): MockInterface
    {
        return (new RepositoryMockBuilder($repositoryClass, $entityClass))->build();
    }
}
