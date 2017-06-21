<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\UpdateDetails as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * @covers \Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication\UpdateDetails
 */
class UpdateDetailsTest extends CommandHandlerTestCase
{
    protected $loggedInUser;

    /** @var  CommandHandler\TransportManagerApplication\UpdateDetails */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CommandHandler\TransportManagerApplication\UpdateDetails();
        $this->mockRepo('TransportManagerApplication', TransportManagerApplication::class);
        $this->mockRepo('ContactDetails', ContactDetails::class);
        $this->mockRepo('Address', \Dvsa\Olcs\Api\Domain\Repository\Address::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = ['TM_TYPE', 'ct_tm', 'tmap_st_awaiting_signature'];

        $this->references = [
            \Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre::class => [
                12 => m::mock(\Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre::class),
                65 => m::mock(\Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre::class),
            ],
            \Dvsa\Olcs\Api\Entity\ContactDetails\Country::class => [
                'DE' => m::mock(\Dvsa\Olcs\Api\Entity\ContactDetails\Country::class),
                'GB' => m::mock(\Dvsa\Olcs\Api\Entity\ContactDetails\Country::class),
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithVersion()
    {
        $command = Command::create(
            [
                'id' => 863,
                'version' => 234,
                'email' => 'fred@fred.com',
                'placeOfBirth' => 'Leeds',
                'dob' => '2015-11-26',
                'homeAddress' => [
                    'addressLine1' => 'LINE_1',
                    'addressLine2' => 'LINE_2',
                    'addressLine3' => 'LINE_3',
                    'addressLine4' => 'LINE_4',
                    'town' => 'TOWN',
                    'postcode' => 'POSTCODE',
                    'countryCode' => 'GB',
                ],
                'workAddress' => [
                    'addressLine1' => 'W_LINE_1',
                    'addressLine2' => 'W_LINE_2',
                    'addressLine3' => 'W_LINE_3',
                    'addressLine4' => 'W_LINE_4',
                    'town' => 'W_TOWN',
                    'postcode' => 'W_POSTCODE',
                    'countryCode' => 'DE',
                ],
                'tmType' => 'TM_TYPE',
                'isOwner' => 'Y',
                'hoursMon' => 1,
                'hoursTue' => 2,
                'hoursWed' => 3,
                'hoursThu' => 4,
                'hoursFri' => 5,
                'hoursSat' => 6,
                'hoursSun' => 7,
                'additionalInfo' => 'TEXT',
                'submit' => 'Y',

            ]
        );

        $tma = new \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication();
        $tma->setId(863);
        $tma->setTransportManager(new \Dvsa\Olcs\Api\Entity\Tm\TransportManager());
        $tma->getTransportManager()->setHomeCd(
            new \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails(
                m::mock(\Dvsa\Olcs\Api\Entity\System\RefData::class)
            )
        );
        $tma->getTransportManager()->getHomeCd()->setPerson(new \Dvsa\Olcs\Api\Entity\Person\Person());

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->once()
            ->with($command, \Doctrine\ORM\Query::HYDRATE_OBJECT, 234)->andReturn($tma);

        $this->repoMap['Address']->shouldReceive('save')->once();
        $this->repoMap['ContactDetails']->shouldReceive('save')->once();

        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Transport Manager Application ID 863 updated'], $result->getMessages());
    }

    public function testHandleCommandWithException()
    {
        $this->setExpectedException(ValidationException::class);

        $command = Command::create(
            [
                'id' => 863,
                'version' => 234,
                'email' => 'fred@fred.com',
                'placeOfBirth' => 'Leeds',
                'dob' => null,
                'homeAddress' => [
                    'addressLine1' => 'LINE_1',
                    'addressLine2' => 'LINE_2',
                    'addressLine3' => 'LINE_3',
                    'addressLine4' => 'LINE_4',
                    'town' => 'TOWN',
                    'postcode' => 'POSTCODE',
                    'countryCode' => 'GB',
                ],
                'workAddress' => [
                    'addressLine1' => 'W_LINE_1',
                    'addressLine2' => 'W_LINE_2',
                    'addressLine3' => 'W_LINE_3',
                    'addressLine4' => 'W_LINE_4',
                    'town' => 'W_TOWN',
                    'postcode' => 'W_POSTCODE',
                    'countryCode' => 'DE',
                ],
                'tmType' => 'TM_TYPE',
                'isOwner' => 'Y',
                'hoursMon' => 1,
                'hoursTue' => 2,
                'hoursWed' => 3,
                'hoursThu' => 4,
                'hoursFri' => 5,
                'hoursSat' => 6,
                'hoursSun' => 7,
                'additionalInfo' => 'TEXT',
                'submit' => 'Y',

            ]
        );

        $tma = new \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication();
        $tma->setId(863);
        $tma->setTransportManager(new \Dvsa\Olcs\Api\Entity\Tm\TransportManager());
        $tma->getTransportManager()->setHomeCd(
            new \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails(
                m::mock(\Dvsa\Olcs\Api\Entity\System\RefData::class)
            )
        );
        $tma->getTransportManager()->getHomeCd()->setPerson(new \Dvsa\Olcs\Api\Entity\Person\Person());

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->once()
            ->with($command, \Doctrine\ORM\Query::HYDRATE_OBJECT, 234)->andReturn($tma);

        $this->repoMap['ContactDetails']->shouldReceive('save')->once();
        $this->repoMap['Address']->shouldReceive('save')->once();

        $this->sut->handleCommand($command);
    }
}
