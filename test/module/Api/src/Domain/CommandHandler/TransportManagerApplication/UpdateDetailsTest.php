<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails;
use Dvsa\Olcs\Api\Entity\Application\PreviousConviction;
use Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence;
use Dvsa\Olcs\Api\Entity\Tm\TmEmployment;
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

    public function setUp(): void
    {
        $this->sut = new CommandHandler\TransportManagerApplication\UpdateDetails();
        $this->mockRepo('TransportManagerApplication', TransportManagerApplication::class);
        $this->mockRepo('ContactDetails', ContactDetails::class);
        $this->mockRepo('Address', \Dvsa\Olcs\Api\Domain\Repository\Address::class);
        $this->mockRepo('TmEmployment', \Dvsa\Olcs\Api\Domain\Repository\TmEmployment::class);
        $this->mockRepo('OtherLicence', \Dvsa\Olcs\Api\Domain\Repository\OtherLicence::class);
        $this->mockRepo('PreviousConviction', \Dvsa\Olcs\Api\Domain\Repository\PreviousConviction::class);
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

    /**
     * @dataProvider commandDataProvider
     */
    public function testHandleCommand($commandData)
    {
        $command = Command::create($commandData);
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

        if ($commandData['dob'] === null) {
            $this->expectException(ValidationException::class);
            $this->sut->handleCommand($command);
        } else {
            $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once();
            $this->maybeDeleteAssociatedData($commandData);
            $result = $this->sut->handleCommand($command);
            $this->assertSame(['Transport Manager Application ID 863 updated'], $result->getMessages());
        }
    }

    private function maybeDeleteAssociatedData($commandData)
    {
        if ($commandData['submit'] !== 'Y') {
            return;
        }
        if ($commandData['hasOtherLicences'] === 'N') {
            $otherLicence = m::mock(OtherLicence::class);
            $this->repoMap['OtherLicence']
                ->shouldReceive('fetchForTransportManagerApplication')
                ->andReturn([$otherLicence]);
            $this->repoMap['OtherLicence']
                ->shouldReceive('delete')
                ->with($otherLicence);
        }
        if ($commandData['hasPreviousLicences'] === 'N') {
            $previousLicence = m::mock(OtherLicence::class);
            $this->repoMap['OtherLicence']
                ->shouldReceive('fetchByTransportManager')
                ->andReturn([$previousLicence]);
            $this->repoMap['OtherLicence']
                ->shouldReceive('delete')
                ->with($previousLicence);
        }
        if ($commandData['hasOtherEmployment'] === 'N') {
            $otherEmployment = m::mock(TmEmployment::class);
            $this->repoMap['TmEmployment']
                ->shouldReceive('fetchByTransportManager')
                ->andReturn([$otherEmployment]);
            $this->repoMap['TmEmployment']
                ->shouldReceive('delete')
                ->with($otherEmployment);
        }
        if ($commandData['hasConvictions'] === 'N') {
            $previousConviction = m::mock(PreviousConviction::class);
            $this->repoMap['PreviousConviction']
                ->shouldReceive('fetchByTransportManager')
                ->andReturn([$previousConviction]);
            $this->repoMap['PreviousConviction']
                ->shouldReceive('delete')
                ->with($previousConviction);
        }
    }

    public function commandDataProvider()
    {
        $commands = [
            'valid_command' => [
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
                    'hasOtherLicences' => null,
                    'hasOtherEmployment' => null,
                    'hasConvictions' => null,
                    'hasPreviousLicences' => null,
                    'additionalInfo' => 'TEXT',
                    'submit' => 'Y',

                ]
            ],
            'invalid_command' => [
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
                    'hasOtherLicences' => null,
                    'hasOtherEmployment' => null,
                    'hasConvictions' => null,
                    'hasPreviousLicences' => null,
                    'additionalInfo' => 'TEXT',
                    'submit' => 'Y',

                ]
            ],
            'valid_command_with_associated_data_no_delete' => [
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
                    'hasOtherLicences' => 'Y',
                    'hasOtherEmployment' => 'Y',
                    'hasConvictions' => 'Y',
                    'hasPreviousLicences' => 'Y',
                    'additionalInfo' => 'TEXT',
                    'submit' => 'Y',
                ]
            ],
            'valid_command_with_associated_data_no_delete_no_submit' => [
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
                    'hasOtherLicences' => 'Y',
                    'hasOtherEmployment' => 'Y',
                    'hasConvictions' => 'Y',
                    'hasPreviousLicences' => 'Y',
                    'additionalInfo' => 'TEXT',
                    'submit' => 'N',
                ]
            ],
            'valid_command_with_associated_data_delete_on_submit' => [
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
                    'hasOtherLicences' => 'N',
                    'hasOtherEmployment' => 'N',
                    'hasConvictions' => 'N',
                    'hasPreviousLicences' => 'N',
                    'additionalInfo' => 'TEXT',
                    'submit' => 'Y',
                ]
            ],
        ];

        return $commands;
    }
}
