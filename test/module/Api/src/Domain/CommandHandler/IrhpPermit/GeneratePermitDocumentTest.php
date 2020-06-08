<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermit;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\IrhpPermit\GeneratePermitDocument as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermit\GeneratePermitDocument as Sut;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as CountryEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as IrhpPermitEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * GeneratePermitDocumentTest
 */
class GeneratePermitDocumentTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);

        parent::setUp();
    }

    /**
    * @dataProvider dpHandleCommand
    */
    public function testHandleCommand(
        $irhpPermitTypeId,
        $countryId,
        $expectedTemplate,
        $expectedDescription,
        $expectedMessages
    ) {
        $irhpPermitId = 1;
        $permitNo = 123;
        $licenceId = 10;
        $orgId = 11;
        $irhpPermitStockId = 100;

        $command = Cmd::Create(
            [
                'irhpPermit' => $irhpPermitId
            ]
        );

        $irhpPermitType = m::mock(IrhpPermitTypeEntity::class);
        $irhpPermitType->shouldReceive('getId')
            ->andReturn($irhpPermitTypeId);

        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);
        $irhpPermitStock->shouldReceive('getId')
            ->andReturn($irhpPermitStockId)
            ->shouldReceive('getIrhpPermitType')
            ->andReturn($irhpPermitType)
            ->shouldReceive('getCountry->getId')
            ->andReturn($countryId);

        $licence = m::mock(LicenceEntity::class);
        $licence->shouldReceive('getId')
            ->andReturn($licenceId)
            ->shouldReceive('getOrganisation->getId')
            ->andReturn($orgId);

        $irhpPermitApplication = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->andReturn($irhpPermitStock);
        $irhpPermitApplication->shouldReceive('getIrhpApplication->getLicence')
            ->andReturn($licence);

        $irhpPermit = m::mock(IrhpPermitEntity::class);
        $irhpPermit->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication);
        $irhpPermit->shouldReceive('getId')->andReturn($irhpPermitId);
        $irhpPermit->shouldReceive('getPermitNumber')->andReturn($permitNo);

        $this->repoMap['IrhpPermit']->shouldReceive('fetchById')
            ->with($irhpPermitId, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit);

        $this->expectedSideEffect(
            GenerateAndStore::class,
            [
                'template' => $expectedTemplate,
                'query' => [
                    'licence' => $licenceId,
                    'irhpPermit' => $irhpPermitId,
                    'irhpPermitStock' => $irhpPermitStockId,
                    'organisation' => $orgId,
                ],
                'knownValues' => [],
                'description' => $expectedDescription,
                'category' => CategoryEntity::CATEGORY_PERMITS,
                'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_PERMIT,
                'isExternal' => false,
                'isScan' => false
            ],
            (new Result())->addId('document', 100)->addMessage('Document generated')
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
        $expected = [
            'id' => [
                'permit' => 100,
            ],
            'messages' => $expectedMessages
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function dpHandleCommand()
    {
        return [
            'ECMT Annual' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT,
                'countryId' => null,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ECMT,
                'expectedDescription' => 'IRHP PERMIT ECMT 123',
                'expectedMessages' => [
                    'IRHP PERMIT ECMT 123 RTF created and stored',
                ],
            ],
            'ECMT Short-term' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                'countryId' => null,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_SHORT_TERM_ECMT,
                'expectedDescription' => 'IRHP PERMIT SHORT TERM ECMT 123',
                'expectedMessages' => [
                    'IRHP PERMIT SHORT TERM ECMT 123 RTF created and stored',
                ],
            ],
            'ECMT Removal' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                'countryId' => null,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ECMT_REMOVAL,
                'expectedDescription' => 'IRHP PERMIT ECMT REMOVALS 123',
                'expectedMessages' => [
                    'IRHP PERMIT ECMT REMOVALS 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Austria' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_AUSTRIA,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_AUSTRIA,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT AUSTRIA 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT AUSTRIA 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Belgium' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_BELGIUM,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_BELGIUM,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT BELGIUM 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT BELGIUM 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Bulgaria' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_BULGARIA,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_BULGARIA,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT BULGARIA 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT BULGARIA 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Croatia' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_CROATIA,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_CROATIA,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT CROATIA 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT CROATIA 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Cyprus' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_CYPRUS,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_CYPRUS,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT CYPRUS 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT CYPRUS 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Czech Republic' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_CZECH_REPUBLIC,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_CZECH_REPUBLIC,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT CZECH REPUBLIC 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT CZECH REPUBLIC 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Denmark' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_DENMARK,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_DENMARK,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT DENMARK 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT DENMARK 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Estonia' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_ESTONIA,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_ESTONIA,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT ESTONIA 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT ESTONIA 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Finland' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_FINLAND,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_FINLAND,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT FINLAND 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT FINLAND 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - France' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_FRANCE,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_FRANCE,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT FRANCE 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT FRANCE 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Germany' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_GERMANY,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_GERMANY,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT GERMANY 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT GERMANY 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Greece' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_GREECE,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_GREECE,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT GREECE 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT GREECE 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Hungary' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_HUNGARY,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_HUNGARY,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT HUNGARY 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT HUNGARY 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Iceland' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_ICELAND,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_ICELAND,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT ICELAND 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT ICELAND 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Ireland' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_IRELAND,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_IRELAND,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT IRELAND 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT IRELAND 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Italy' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_ITALY,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_ITALY,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT ITALY 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT ITALY 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Latvia' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_LATVIA,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_LATVIA,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT LATVIA 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT LATVIA 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Liechtenstein' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_LIECHTENSTEIN,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_LIECHTENSTEIN,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT LIECHTENSTEIN 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT LIECHTENSTEIN 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Lithuania' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_LITHUANIA,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_LITHUANIA,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT LITHUANIA 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT LITHUANIA 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Luxembourg' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_LUXEMBOURG,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_LUXEMBOURG,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT LUXEMBOURG 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT LUXEMBOURG 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Malta' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_MALTA,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_MALTA,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT MALTA 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT MALTA 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Netherlands' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_NETHERLANDS,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_NETHERLANDS,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT NETHERLANDS 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT NETHERLANDS 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Norway' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_NORWAY,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_NORWAY,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT NORWAY 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT NORWAY 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Poland' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_POLAND,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_POLAND,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT POLAND 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT POLAND 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Portugal' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_PORTUGAL,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_PORTUGAL,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT PORTUGAL 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT PORTUGAL 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Romania' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_ROMANIA,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_ROMANIA,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT ROMANIA 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT ROMANIA 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Slovakia' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_SLOVAKIA,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_SLOVAKIA,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT SLOVAKIA 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT SLOVAKIA 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Slovenia' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_SLOVENIA,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_SLOVENIA,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT SLOVENIA 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT SLOVENIA 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Spain' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_SPAIN,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_SPAIN,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT SPAIN 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT SPAIN 123 RTF created and stored',
                ],
            ],
            'IRHP Bilateral - Sweden' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'countryId' => CountryEntity::ID_SWEDEN,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_SWEDEN,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT SWEDEN 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT SWEDEN 123 RTF created and stored',
                ],
            ],
            'IRHP Multilateral' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                'countryId' => null,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_MULTILAT,
                'expectedDescription' => 'IRHP PERMIT ANN MULTILATERAL 123',
                'expectedMessages' => [
                    'IRHP PERMIT ANN MULTILATERAL 123 RTF created and stored',
                ],
            ],
        ];
    }

    public function testHandleCommandForUndefinedTemplate()
    {
        $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\RuntimeException::class);
        $this->expectExceptionMessage('Permit template not defined for IRHP Permit Type (id: undefined)');

        $irhpPermitTypeId = 'undefined';
        $irhpPermitId = 1;

        $command = Cmd::Create(
            [
                'irhpPermit' => $irhpPermitId
            ]
        );

        $irhpPermitType = m::mock(IrhpPermitTypeEntity::class);
        $irhpPermitType->shouldReceive('getId')
            ->andReturn($irhpPermitTypeId);

        $irhpPermitApplication = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);

        $irhpPermit = m::mock(IrhpPermitEntity::class);
        $irhpPermit->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication);

        $this->repoMap['IrhpPermit']->shouldReceive('fetchById')
            ->with($irhpPermitId, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit);

        $this->sut->handleCommand($command);
    }
}
