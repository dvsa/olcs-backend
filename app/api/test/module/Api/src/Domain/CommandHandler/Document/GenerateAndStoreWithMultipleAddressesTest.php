<?php


namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\Document;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator;
use Dvsa\Olcs\Api\Service\Document\NamingService;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStoreWithMultipleAddresses as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\GenerateAndStoreWithMultipleAddresses as CommandHandler;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

class GenerateAndStoreWithMultipleAddressesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Document', Document::class);
        $this->mockRepo('Licence', Licence::class);

        $this->mockedSmServices['DocumentNamingService'] = m::mock(NamingService::class);
        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);
        $this->mockedSmServices['DocumentGenerator'] = m::mock(DocumentGenerator::class);

        parent::setUp();
    }

    /**
     * testHandleCommand
     */
    public function testHandleCommand()
    {

        $mockCommand = m::mock(Cmd::class);
        $mockLicence = m::mock(Licence::class);

        $this->setUpMockLicence($mockLicence);


        $this->setUpMockCommand($mockCommand);

        $result = new Result();

        foreach ($this->getAddresses() as $address) {
            $this->expectedSideEffect(GenerateAndStore::class, [], $result);
        }


        $this->sut->handleCommand($mockCommand);
    }


    protected function getAddresses()
    {
        $addresses = [];

        foreach ([
                     "correspondenceAddress",
                     "establishmentAddress",
                     "transportConsultantAddress",
                     "registeredAddress",
                     "operatingCentreAddress"
                 ] as $addressType) {
            $addresses[$addressType] = $this->addressProvider($addressType)[$addressType];
        }


        return $addresses;
    }

    protected function addressProvider($addressType, $skipCorrespondence = false)
    {

        if ($addressType === 'operatingCentresAddresses') {
            return [
                m::mock(LicenceOperatingCentre::class)->shouldReceive('getOperatingCentre')->andReturn(
                    m::mock(OperatingCentre::class)->shouldReceive('getAddress')->andReturn(
                        $this->addressProvider('operatingCentreAddress')
                    )->getMock()
                )->getMock()
            ];
        }
        $contactDetails = m::mock(ContactDetails::class)->makePartial();

        $address = new Address();
        $address->updateAddress(
            "DVSA - " . $addressType,
            "THe Axis Building",
            "Nottingham",
            "Nottinghamshire",
            "Nottingham",
            "NG1 6LP",
            new Country()
        );
        if ($skipCorrespondence) {
            $address = new Address();
        }
        $contactDetails->setAddress($address);
        return [$addressType => $contactDetails];
    }

    public function testEmptyAddress()
    {

        $mockCommand = m::mock(Cmd::class);
        $this->setUpMockCommand($mockCommand);
        $mockLicence = m::mock(Licence::class);
        $this->setUpMockLicence($mockLicence, true);
        $result = new Result();
        $addresses = $this->getAddresses();
        $addresses['correspondenceAddress']->getAddress()->setAddressLine1(null);
        $count = 0;
        foreach ($addresses as $key => $address) {
            if ($key != 'correspondenceAddress') {
                $this->expectedSideEffect(GenerateAndStore::class, [], $result);
            }
            $count++;
        }
        $this->sut->handleCommand($mockCommand);
    }

    /**
     * getMockLicence
     *
     * @param $mockLicence
     */
    protected function setUpMockLicence($mockLicence, $noCorrespondanceAddress = false): void
    {
        $this->repoMap['Licence']->shouldReceive('fetchWithAddressesUsingId')->andReturn(
            $mockLicence->shouldReceive('getCorrespondenceCd')->andReturn(
                $this->addressProvider('correspondenceAddress', $noCorrespondanceAddress)['correspondenceAddress']
            )->getMock()
                ->shouldReceive('getEstablishmentCd')
                ->andReturn(
                    $this->addressProvider('establishmentAddress')['establishmentAddress']
                )
                ->getMock()
                ->shouldReceive('getTransportConsultantCd')
                ->andReturn(
                    $this->addressProvider('transportConsultantAddress')['transportConsultantAddress']
                )
                ->getMock()
                ->shouldReceive('getOrganisation')
                ->andReturn(
                    m::mock(Organisation::class)->shouldReceive('getContactDetails')->andReturn(
                        $this->addressProvider('registeredAddress')['registeredAddress']
                    )->getMock()
                )
                ->getMock()
        );
        $mockLicence->shouldReceive('getOperatingCentres')->andReturn(
            [
                m::mock(LicenceOperatingCentre::class)->shouldReceive('getOperatingCentre')->andReturn(
                    m::mock(OperatingCentre::class)->shouldReceive('getAddress')->andReturn(
                        m::mock(Address::class)->shouldReceive('serialize')->andReturn(
                            $this->addressProvider('operatingCentreAddress')['operatingCentreAddress']->getAddress()->serialize()
                        )->getMock()
                    )->getMock()
                )->getMock()
            ]
        );
    }

    /**
     * setUpMockCommand
     *
     * @param $mockCommand
     */
    private function setUpMockCommand($mockCommand): void
    {
        $mockCommand->shouldReceive('getGenerateCommandData')->andReturn(
            [
                'description' => ' Last TM letter Licence 1',
                'licence' => 1
            ]
        )->getMock()
            ->shouldReceive('getAddressBookmark')->andReturn('licence_holder_address')
            ->getMock()
            ->shouldReceive('getBookmarkBundle')->andReturn([
                'correspondenceCd' => ['address']
            ])->getMock()
            ->shouldReceive('getSendToAddresses')->andReturn($this->getAddresses());
    }
}
