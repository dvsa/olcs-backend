<?php


namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStoreWithMultipleAddresses;
use Dvsa\Olcs\Api\Domain\Repository\Document;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
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

        $this->repoMap['Licence']->shouldReceive('fetchWithAddressesUsingId')->andReturn(
            m::mock(Licence::class)->shouldReceive('getCorrespondenceCd')->andReturn(
                $this->addressProvider('correspondenceAddress')['correspondenceAddress']
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
                ->shouldReceive('getOperatingCentres')
                ->andReturn(
                    [
                        m::mock(LicenceOperatingCentre::class)->shouldReceive('getOperatingCentre')
                        ->andReturn(
                            null
                        )
                    ]
                )
                ->getMock()
        );

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

        $this->sut->handleCommand($mockCommand);
    }


    protected function getAddresses()
    {
        $addresses = [];

        foreach ([
                     "correspondenceAddress",
                     "establishmentAddress",
                     "transportConsultantAddress",
                     "registeredAddress"
                 ] as $addressType) {
            $addresses[$addressType] = $this->addressProvider($addressType)[$addressType];
        }
        return $addresses;
    }

    protected function addressProvider($addressType)
    {
        $contactDetails = m::mock(ContactDetails::class)->makePartial();
        $address = new Address();
        $address->updateAddress(
            "DVSA",
            "THe Axis Building",
            "Nottingham",
            null,
            "Nottingham",
            "NG1 6LP",
            null
        );
        $contactDetails->setAddress($address);


        return [$addressType => $contactDetails];
    }
}