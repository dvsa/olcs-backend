<?php

/**
 * Save Addresses test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\SaveAddresses;

use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;

use Dvsa\Olcs\Api\Domain\Command\Licence\SaveAddresses as Cmd;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;

/**
 * Save Addresses test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class SaveAddressesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new SaveAddresses();
        $this->mockRepo('Licence', Licence::class);
        $this->mockRepo('ContactDetails', ContactDetails::class);

        parent::setUp();
    }

    public function testHandleCommandWithFullyPopulatedNewData()
    {
        $data = [
            'id' => 10,
            'correspondence' => [
                'id' => '',
                'version' => '',
                'fao' => 'foo bar'
            ],
            'correspondenceAddress' => [
                'id' => '',
                'version' => '',
                'addressLine1' => 'Address 1',
                'town' => 'Leeds',
                'postcode' => 'LS9 6NF',
                'countryCode' => 'GB',
            ],
            'contact' => [
                'phone_business' => '01131231234',
                'phone_business_id' => '',
                'phone_business_version' => '',

                'phone_home' => '01131231234',
                'phone_home_id' => '',
                'phone_home_version' => '',

                'phone_mobile' => '01131231234',
                'phone_mobile_id' => '',
                'phone_mobile_version' => '',

                'phone_fax' => '01131231234',
                'phone_fax_id' => '',
                'phone_fax_version' => '',

                'email' => 'contact@email.com'
            ],
            'establishment' => [
                'id' => '',
                'version' => ''
            ],
            'establishmentAddress' => [
                'id' => '',
                'version' => '',
                'addressLine1' => 'Est Address 1',
                'town' => 'Est Leeds',
                'postcode' => 'LS8 5NF',
                'countryCode' => 'GB',
            ],
            'consultant' => [
                'add-transport-consultant' => 'Y',
                'writtenPermissionToEngage' => 'Y',
                'transportConsultantName' => 'A TC',
                'address' => [
                    'id' => '',
                    'version' => '',
                    'addressLine1' => 'Est Address 1',
                    'town' => 'Est Leeds',
                    'postcode' => 'LS8 5NF',
                    'countryCode' => 'GB',
                ],
                'contact' => [
                    'phone_business' => '01131231234',
                    'phone_business_id' => '',
                    'phone_business_version' => '',

                    'phone_home' => '01131231234',
                    'phone_home_id' => '',
                    'phone_home_version' => '',

                    'phone_mobile' => '01131231234',
                    'phone_mobile_id' => '',
                    'phone_mobile_version' => '',

                    'phone_fax' => '01131231234',
                    'phone_fax_id' => '',
                    'phone_fax_version' => '',

                    'email' => 'tc@email.com'
                ]
            ]
        ];

        $command = Cmd::create($data);

        $correspondenceCd = m::mock(ContactDetailsEntity::class)
            ->shouldReceive('setFao')
            ->with('foo bar')
            ->shouldReceive('setEmailAddress')
            ->with('contact@email.com')
            ->shouldReceive('getVersion')
            ->andReturn(null, 1)
            ->getMock();

        $licence = m::mock(LicenceEntity::class)
            ->makePartial()
            ->shouldReceive('setCorrespondenceCd')
            ->with('')
            ->shouldReceive('getCorrespondenceCd')
            ->andReturn($correspondenceCd)
            ->getMock();

        $result = new Result();
        $result->addId('contactDetails', 123);

        $this->expectedSideEffect(
            SaveAddress::class,
            [
                'id' => '',
                'version' => '',
                'addressLine1' => 'Address 1',
                'town' => 'Leeds',
                'postcode' => 'LS9 6NF',
                'countryCode' => 'GB',
                'contactType' => 'ct_corr'
            ],
            $result
        );

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence)
            ->once()
            ->shouldReceive('save')
            ->with($licence)
            ->once()
            ->getMock();

        $this->repoMap['ContactDetails']->shouldReceive('save')
            ->with($correspondenceCd);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => ['Application saved successfully']
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
