<?php

/**
 * Create Unlicensed Operator Command Handler Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Operator;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Operator\CreateUnlicensed;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails as ContactDetailsRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\LicenceNoGen as LicenceNoGenRepo;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Licence\LicenceNoGen as LicenceNoGenEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Transfer\Command\Operator\CreateUnlicensed as CreateUnlicensedCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Create Unlicensed Operator Command Handler Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CreateUnlicensedTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateUnlicensed();
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('ContactDetails', ContactDetailsRepo::class);
        $this->mockRepo('LicenceNoGen', LicenceNoGenRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            ContactDetailsEntity::CONTACT_TYPE_CORRESPONDENCE_ADDRESS,
            LicenceEntity::LICENCE_TYPE_RESTRICTED,
            LicenceEntity::LICENCE_STATUS_UNLICENSED,
            LicenceEntity::LICENCE_CATEGORY_PSV,
            OrganisationEntity::ORG_TYPE_OTHER,
        ];

        $this->references = [
            TrafficAreaEntity::class => [
                'N' => m::mock(TrafficAreaEntity::class)->makePartial(),
            ],
            ContactDetailsEntity::class => [
                1 => m::mock(ContactDetailsEntity::class)->makePartial(),
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $licenceNoGenId = 1234567;

        $contactDetails = [
            'emailAddress' => 'foo@bar.com',
            'address' => [
                'addressLine1' => 'al1',
                'addressLine2' => 'al2',
                'addressLine3' => 'al3',
                'addressLine4' => 'al4',
                'town' => 'atown',
                'postcode' => 'pc',
            ],
            'phoneContacts' => [
                //@todo
            ],
        ];

        $data = [
            'name' => 'name',
            'operatorType' => 'lcat_psv',
            'trafficArea' => 'N',
            'contactDetails' => $contactDetails,
        ];

        $expectedLicenceNo = 'UPN1234567';

        $command = CreateUnlicensedCmd::create($data);

        $savedLicence = null;

        $this->repoMap['Licence']->shouldReceive('save')
            ->with(m::type(LicenceEntity::class))
            ->andReturnUsing(
                function (LicenceEntity $licence) use (&$savedLicence) {
                    $licence->setId(1);
                    $savedLicence = $licence;
                }
            )
            ->twice(); // saved twice due to licence no. generation

        $this->repoMap['ContactDetails']
            ->shouldReceive('populateRefDataReference')
            ->with($contactDetails)
            ->andReturn($contactDetails);

        $savedLicenceNoGen = null;

        $this->repoMap['LicenceNoGen']->shouldReceive('save')
            ->with(m::type(LicenceNoGenEntity::class))
            ->andReturnUsing(
                function (LicenceNoGenEntity $licenceNoGen) use (&$savedLicenceNoGen, $licenceNoGenId) {
                    $licenceNoGen->setId($licenceNoGenId);
                    $savedLicenceNoGen = $licenceNoGen;
                }
            )
            ->once();

        // invoke
        $result = $this->sut->handleCommand($command);

        // assertions...

        // assert response details
        $this->assertEquals(1, $result->getIds()['licence']);
        $this->assertEquals(
            [
              'Licence added',
              'Organisation added',
              'ContactDetails added',
              'Address added',
            ],
            $result->getMessages()
        );

        // assert organisation record properties
        $this->assertEquals('name', $savedLicence->getOrganisation()->getName());
        $this->assertEquals(OrganisationEntity::ORG_TYPE_OTHER, $savedLicence->getOrganisation()->getType()->getId());
        $this->assertTrue($savedLicence->getOrganisation()->isUnlicensed());

        // assert licence record properties
        $this->assertTrue($savedLicence->isPsv());
        $this->assertEquals(LicenceEntity::LICENCE_TYPE_RESTRICTED, $savedLicence->getLicenceType()->getId());
        $this->assertEquals('N', $savedLicence->getTrafficArea()->getId());
        $this->assertEquals($expectedLicenceNo, $savedLicence->getLicNo());
        $this->assertEquals('Y', $savedLicence->getNiFlag());

        // assert contact details record properties
        $this->assertEquals(
            ContactDetailsEntity::CONTACT_TYPE_CORRESPONDENCE_ADDRESS,
            $savedLicence->getCorrespondenceCd()->getContactType()->getId()
        );
        $this->assertEquals('foo@bar.com', $savedLicence->getCorrespondenceCd()->getEmailAddress());
        $this->assertEquals(
            [
                'addressLine1' => 'al1',
                'addressLine2' => 'al2',
                'addressLine3' => 'al3',
                'addressLine4' => 'al4',
                'town' => 'atown',
                'postcode' => 'pc',
                'countryCode' => null,
            ],
            $savedLicence->getCorrespondenceCd()->getAddress()->toArray()
        );
        $this->assertEquals(
            [
                //@todo
            ],
            $savedLicence->getCorrespondenceCd()->getPhoneContacts()->toArray()
        );
    }
}
