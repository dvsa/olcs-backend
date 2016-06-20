<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\Application;

use Dvsa\Olcs\Api\Service\Publication\Context\Application\ConditionUndertaking as ConditionUndertakingContext;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre as OperatingCentreEntity;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking as ConditionUndertakingEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Service\Helper\FormatAddress;

/**
 * Class ConditionUndertakingTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ConditionUndertakingTest extends MockeryTestCase
{
    /**
     * @group publicationFilter
     * @dataProvider provideTestProvider
     *
     * Test the application condition undertakings filter
     *
     * @param $action
     * @param $expectedActionString
     */
    public function testProvideWithOperatingCentre($action, $expectedActionString)
    {
        $conditionTypeDescription = 'condition type description';
        $notes = 'notes';
        $ocAddress = 'oc address';

        $addressEntityMock = m::mock(AddressEntity::class);
        $operatingCentre = m::mock(OperatingCentreEntity::class);
        $operatingCentre->shouldReceive('getAddress')->andReturn($addressEntityMock);

        $conditionUndertaking = m::mock(ConditionUndertakingEntity::class);
        $conditionUndertaking->shouldReceive('getNotes')->andReturn($notes);
        $conditionUndertaking->shouldReceive('getOperatingCentre')->andReturn($operatingCentre);
        $conditionUndertaking->shouldReceive('getConditionType->getDescription')->andReturn($conditionTypeDescription);
        $conditionUndertaking->shouldReceive('getAction')->andReturn($action);

        $conditionUndertakings = new ArrayCollection([$conditionUndertaking]);

        $mockAddressFormatter = m::mock(FormatAddress::class);
        $mockAddressFormatter->shouldReceive('format')->andReturn($ocAddress);

        $publication = m::mock(PublicationLink::class);
        $publication->shouldReceive('getApplication->getConditionUndertakings')->andReturn($conditionUndertakings);

        $sut = new ConditionUndertakingContext(m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class));
        $sut->setAddressFormatter($mockAddressFormatter);

        $output = [
            'conditionUndertaking' => [
                0 => sprintf($expectedActionString, $conditionTypeDescription, $notes) .
                    ' Attached to Operating Centre: ' . $ocAddress
            ]
        ];

        $expectedOutput = new \ArrayObject($output);

        $this->assertEquals($expectedOutput, $sut->provide($publication, new \ArrayObject()));
    }

    /**
     * @group publicationFilter
     *
     * Test the application condition undertakings filter
     */
    public function testProvideUpdateNoOperatingCentre()
    {
        $sut = new ConditionUndertakingContext(m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class));

        $conditionTypeDescription = 'condition type description';
        $notes = 'notes';
        $action = 'U';
        $expectedActionString = $sut::COND_UPDATE;
        $operatingCentre = null;

        $conditionUndertaking = m::mock(ConditionUndertakingEntity::class);
        $conditionUndertaking->shouldReceive('getNotes')->andReturn($notes);
        $conditionUndertaking->shouldReceive('getOperatingCentre')->andReturn($operatingCentre);
        $conditionUndertaking->shouldReceive('getConditionType->getDescription')->andReturn($conditionTypeDescription);
        $conditionUndertaking->shouldReceive('getAction')->andReturn($action);

        $conditionUndertakings = new ArrayCollection([$conditionUndertaking]);

        $publication = m::mock(PublicationLink::class);
        $publication->shouldReceive('getApplication->getConditionUndertakings')->andReturn($conditionUndertakings);

        $output = [
            'conditionUndertaking' => [
                0 => sprintf($expectedActionString, $conditionTypeDescription, $notes) .
                    ' Attached to Licence. ' . sprintf($sut::COND_AMENDED, $notes)
            ]
        ];

        $expectedOutput = new \ArrayObject($output);

        $this->assertEquals($expectedOutput, $sut->provide($publication, new \ArrayObject()));
    }

    /**
     * Provider for testProvide
     *
     * @return array
     */
    public function provideTestProvider()
    {
        $sut = new ConditionUndertakingContext(m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class));

        return [
            ['A', $sut::COND_NEW],
            ['D', $sut::COND_REMOVE],
            ['ZZZ', $sut::COND_NEW]
        ];
    }
}
