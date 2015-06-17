<?php

/**
 * ContactDetails Repo test
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails as Repo;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as CountryEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;

/**
 * ContactDetails Repo test
 */
class ContactDetailsTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testPopulateRefDataReference()
    {
        $data = [
            'address' => ['countryCode' => 'GB'],
            'phoneContacts' => [
                ['phoneContactType' => 'phone_t_tel']
            ]
        ];

        $countryEntity = m::mock(CountryEntity::class);
        $refDataEntity = m::mock(RefDataEntity::class);

        $this->em->shouldReceive('getReference')
            ->once()
            ->with(CountryEntity::class, 'GB')
            ->andReturn($countryEntity);

        $this->em->shouldReceive('getReference')
            ->once()
            ->with(RefDataEntity::class, 'phone_t_tel')
            ->andReturn($refDataEntity);

        $result = $this->sut->populateRefDataReference($data);

        $this->assertEquals(
            [
                'address' => ['countryCode' => $countryEntity],
                'phoneContacts' => [
                    ['phoneContactType' => $refDataEntity]
                ]
            ],
            $result
        );
    }
}
